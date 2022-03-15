{$addon=$smarty.request.addon}
<div id="hw-{$addon}-support"></div>
<script>$(document).ready(function(){$ldelim}$.ajax({$ldelim} async :true, type:'POST', data: {$ldelim} addon : '{$addon}' {$rdelim}, url: "https://api.hungryweb.net/ws/support" {$rdelim}).done(function(data){$ldelim} $('#hw-{$addon}-support').html(data); {$rdelim});{$rdelim});</script>