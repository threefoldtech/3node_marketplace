{include file="addons/cp_profile_types/components/select_profile_type.tpl" is_checkout=true target_id="litecheckout_form"}
<div id="litecheckout_step_customer_info" class="litecheckout__group">
    {include
        file="views/checkout/components/profile_fields.tpl"
        profile_fields=$profile_fields
        section="ProfileFieldSections::CONTACT_INFORMATION"|enum
    }
</div>
