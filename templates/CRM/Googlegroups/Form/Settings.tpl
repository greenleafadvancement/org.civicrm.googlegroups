<div class="crm-block crm-form-block crm-googlegroup-setting-form-block">
  <div class="crm-accordion-wrapper crm-accordion_googlegroup_setting-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      {ts}OAuth 2.0 client credentials setup in your Google Developer Console{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      <table class="form-layout-compressed">
        <tr class="crm-googlegroup-setting-api-key-block">
          <td class="label">{$form.client_id.label}</td>
          <td>{$form.client_id.html}<br/>
            <span class="description">{ts}Example format: 1001349160852-769gszch00qi78l9gj2khi2n26qpd4kr.apps.googleusercontent.com{/ts}</span>
          </td>
        </tr>
        <tr class="crm-webinar-setting-api-key-email">
          <td class="label">{$form.client_secret.label}</td>
          <td>{$form.client_secret.html}<br/>
            <span class="description">{ts}Example format: jpLW81FkoE29dqJ_EyK8THKa{/ts}</span>
          </td>
        </tr>
      </table>
    </div>
    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl"}
    </div>
  </div>
</div>
