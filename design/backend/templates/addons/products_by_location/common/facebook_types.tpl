{include file="common/subheader.tpl" title=__("Locations") target="#location_settings"}
<div id="location_settings" class="in collapse">
    <div class="control-group cm-no-hide-input">
        <div class="controls">
            {* Countries list *}
                {include file="common/double_selectboxes.tpl"
                    title=__("countries")
                    first_name="product_location[countries]"
                    first_data=$selected_countries
                    second_name="all_countries"
                    second_data=$all_countries
                    class_name="destination-countries"
                }
        </div>
    </div>
</div>
