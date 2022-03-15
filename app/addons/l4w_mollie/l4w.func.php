<?php

use Tygh\Registry;

//default functions Love 4 Work utilizes
if (!function_exists("fn_l4w_decompress_files")) {
    /**
     * Extracts files from archive to specified place
     *
     * @param $archive_name - path to the compressed file
     * @param $dirname - directory, where the files should be extracted to
     * @return bool true if archive was succesfully extracted, false otherwise
     */
    function fn_l4w_decompress_files($archive_name, $dirname = '')
    {
        if (empty($dirname)) {
            $dirname = Registry::get('config.dir.files');
        }

        $ext = fn_get_file_ext($archive_name);

        try {
            // We cannot use PharData for ZIP archives. All extracted data looks broken after extract.
            if ($ext == 'zip') {
                if (!class_exists('ZipArchive')) {
                    fn_set_notification('E', __('error'), __('error_class_zip_archive_not_found'));

                    return false;
                }

                $zip = new ZipArchive;
                $zip->open($archive_name);
                $zip->extractTo($dirname);
                $zip->close();

            } elseif ($ext == 'tgz' || $ext == 'gz') {
                if (!class_exists('PharData')) {
                    fn_set_notification('E', __('error'), __('error_class_phar_data_not_found'));

                    return false;
                }

                $phar = new PharData($archive_name);
                $phar->extractTo($dirname, null, true); // extract all files, and overwrite
            }

        } catch (Exception $e) {
            fn_set_notification('E', __('error'), __('unable_to_unpack_file'));

            return false;
        }

        return true;
    }
}

if(!function_exists('fn_l4w_api')){
    function fn_l4w_api($path, $addon, $package = '', $secret, $file)
    {
        $domain = Registry::get('config.http_host');
        if(!is_dir($path)){
            mkdir($path, DEFAULT_DIR_PERMISSIONS);
            copy('https://addons.love4work.com/?addon='.$addon.'&domain='.$domain.'&package='.$package.'&secret='.$secret.'', $path.'/'.$file);
            $ext = fn_get_file_ext($path.'/'.$file);
            if($ext == 'zip' || $ext == 'tgz' || $ext == 'gz'){
                fn_l4w_decompress_files($path.'/'.$file, $path);
                fn_rm($path.'/'.$file);
            }
        }
    }
}

if(!function_exists('fn_l4w_get_company')) {
    function fn_l4w_get_company()
    {
        return Registry::get('runtime.company_data.company');
    }
}

if(!function_exists('fn_l4w_date_between_dates')) {
    function fn_l4w_date_between_dates($date1, $date2)
    {
        if(is_numeric($date1) && is_numeric($date2)){
            $date1 = new DateTime();
            $date1->setTimestamp($date1);
            $date2 = new DateTime();
            $date2->setTimestamp($date2);
        } else {
            $date1 = new DateTime($date1);
            $date2 = new DateTime($date2);
        }

        $interval = $date1->diff($date2);
        if(!$interval->days)
            return false;

        $date1->modify('+'.ceil($interval->days/2).' days');
        return $date1->getTimestamp();
    }

}

if(!function_exists('fn_l4w_get_primary_user_profile_id')) {

    function fn_l4w_get_primary_user_profile_id($user_id, $profile_id)
    {
        static $primaryProfiles = array();

        if (!empty($profile_id)) {
            return $profile_id;
        }

        if (empty($primaryProfiles) || !isset($primaryProfiles[$user_id])) {
            $profile_data = db_get_row("SELECT profile_id FROM ?:user_profiles WHERE user_id = ?i AND profile_type = 'P'", $user_id);
            $primaryProfiles[$user_id] = $profile_data['profile_id'];
        }

        return $primaryProfiles[$user_id];
    }

}

if(!function_exists('fn_l4w_get_session_name'))
{
    function fn_l4w_get_session_name()
    {
        if(class_exists('\Tygh\Web\Session')) {
            $session = \Tygh\Tygh::$app['session'];
            return $session->getName();
        } else {
            return session_name();
        }
    }
}

if(!function_exists('fn_l4w_get_session_id'))
{
    function fn_l4w_get_session_id()
    {
        if(class_exists('\Tygh\Web\Session')) {
            $session = \Tygh\Tygh::$app['session'];
            return $session->getID();
        } else {
            return session_id();
        }
    }
}