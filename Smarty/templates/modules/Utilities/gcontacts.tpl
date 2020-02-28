{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aaria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium" id="modal-content-id-1">
<!-- <div class="">
	<div class="slds-notify slds-notify_alert slds-theme_alert-texture" role="alert">
	<h2>
		<svg class="slds-icon slds-icon_small slds-m-right_x-small" aria-hidden="true">
		<use xlink:href="include/LD//assets/icons/utility-sprite/svg/symbols.svg#check"></use>
		</svg><a href="{$OAUTHURL}">{$OAUTHURLMSG}</a>
	</h2>
	</div>
</div>
<br>
-->
	{if $ISADMIN}
		<form role="form" style="margin:0 100px;" method="post">
		<input type="hidden" name="module" value="Utilities">
		<input type="hidden" name="action" value="integration">
		<input type="hidden" name="_op" value="setconfiggcontact">
		<div class="slds-form-element">
			<label class="slds-checkbox_toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom_none">{'_active'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="gcontacts_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
			<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
				<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
			</span>
			</label>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="clientId">{'_clientId'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="clientId" name="clientId" class="slds-input" value="{$clientId}" />
			</div>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="clientSecret">{'_clientSecret'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="clientSecret" name="clientSecret" class="slds-input" value="{$clientSecret}" />
			</div>
		</div>
		<div class="slds-m-top_large">
			<button type="submit" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		</form>
	{/if}
	</div>
</div>
</section>