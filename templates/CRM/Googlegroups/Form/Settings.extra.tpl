<table id="googlegroups_list_block">
  <tr class="custom_field-row googlegroups">
    <td class="label">{$form.googlegroup.label}</td>
    <td class="html-adjust">{$form.googlegroup.html}</td>
  </tr>
</table>

{literal}
<script>
CRM.$(function($) {
  var googlegroupsListBlk = $('#googlegroups_list_block').html();
  $('#googlegroups_list_block').remove();
  googlegroupsListBlk = googlegroupsListBlk.replace("<tbody>", "");
  googlegroupsListBlk = googlegroupsListBlk.replace("</tbody>", "");
  $(googlegroupsListBlk).insertAfter($("input[data-crm-custom='Googlegroup_Settings:Googlegroup_Group']").parent().parent());
  $("input[data-crm-custom='Googlegroup_Settings:Googlegroup_Group']").parent().parent().hide();
  $("#googlegroup").change(function() {
    var group_id = $("#googlegroup :selected").val();
    $("input[data-crm-custom='Googlegroup_Settings:Googlegroup_Group']").val(group_id);
  });
});
</script>
{/literal}
