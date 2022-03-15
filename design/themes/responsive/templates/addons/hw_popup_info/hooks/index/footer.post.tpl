{if $addons.hw_popup_info.testmode==Y || $smarty.cookies.hw_popup_info!=1}
{strip}{assign var="banner" value=$addons.hw_popup_info.banner_id|fn_get_banner_data}{/strip}
<div id="info_popup_modal" class="hidden" title="{$banner.banner}">
    <div class="c_flgs">
        <div class="flags">
            <div class="container" style="width:unset">
            <ul>
               {assign var="countries" value="1"|fn_get_simple_countries}
                {foreach $countries as $code => $country}
                    <div></div>
                    <li>
                        <div class="flag">
                        <img src="{$images_dir}/country-flags-list/{strtolower($code)}.svg"/>
                        </div>
                        <h3>{$country}</h3>
                    </li>
                {/foreach}
              </ul> 
            </div>
        </div>
    </div>
    {/if}
    <style>
        * {
            box-sizing: border-box;
        }
      
   
        .ui-dialog .ui-dialog-buttonpane{
            height:45px;
        }
        #info_popup_modal .container,
        .flags,
        #info_popup_modal li,
        #info_popup_modal ul {
            display: flex;
            align-items: center;
            justify-content: center;
            max-height: 600px;
            overflow: auto;
        }

        .flags {
            width: 100%;
            background: #eee;
        }

        .flags li,
        .flags ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .flags h3 {
            text-transform: capitalize;
            margin-top: 10px;
            margin-bottom: -1px;
            overflow:hidden; 
            white-space:nowrap; 
            text-overflow: ellipsis;
            width: 105px;
        }

        .flags ul {
            flex-wrap: wrap;
            padding-top: 20px;
        }

        .flags li {
            margin: 0.5em;
            padding: 0.5em 1em;
            background: #fff;
            zoom: 0.7;
            transition: transform .2s;
        }
        .flags li:hover {
         transform: scale(1.2); 
         box-shadow: 0 0 10px #9d9595;
         z-index:9999;
        }
        .flags .container,
        .flags li {
            flex-direction: column;
        }

        .flags .text {
            width: 900px;
        }

        .flags .flag {
            width: 100px;
            height: 70px;
            overflow: hidden;
            cursor: pointer;
        }
     
    </style>