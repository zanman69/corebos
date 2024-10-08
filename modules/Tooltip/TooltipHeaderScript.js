/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

var TOOLTIP = {
	/** Tooltip data cache (per module, per field, per record) **/
	_cache : { },

	_status : { },

	_mouseOverTimeOut : 500,

	_relinguishStatusControl : false,

	show : function (node, module, fieldname, recordid) {
		if (TOOLTIP._status[module][recordid][fieldname]) {
			if (TOOLTIP._cache[module][fieldname][recordid]) {
				var tooltipdata = TOOLTIP._cache[module][fieldname][recordid];
				tooltip.tip(node, tooltipdata, recordid, fieldname);
				if (!this._relinguishStatusControl) {
					document.getElementById('status').style.display = 'none';
				}
			}
		}
	},

	hide : function (element, id, fieldname) {
		tooltip.untip(element, id, fieldname);
	},

	_setStatus : function (module, fieldname, recordid, statusflag) {
		if (typeof(TOOLTIP._status[module]) == 'undefined') {
			TOOLTIP._status[module] = {};
		}
		if (typeof(TOOLTIP._status[module][recordid]) == 'undefined') {
			TOOLTIP._status[module][recordid] = {};
		}
		TOOLTIP._status[module][recordid][fieldname] = statusflag;
	},

	handler : function (evtparams) {
		var event_type = evtparams['event'];
		var module = evtparams['module'];
		var fieldname = evtparams['fieldname'];
		var recordid = evtparams['recordid'];
		var node = evtparams['domnode'];
		if (document.getElementById('status').style.display == 'block') {
			this._relinguishStatusControl = true;
		}
		if (evtparams['event'] == 'cell.onmouseover' ) {
			TOOLTIP._setStatus(module, fieldname, recordid, true);
			_VT__TOOLTIP__TIMER = setTimeout(function () {
				TOOLTIP._showForField(node, module, fieldname, recordid);
			}, TOOLTIP._mouseOverTimeOut);
		} else if (evtparams['event'] == 'cell.onmouseout' ) {
			TOOLTIP._setStatus(module, fieldname, recordid, false);
			TOOLTIP.hide(node, recordid, fieldname);
			clearTimeout(_VT__TOOLTIP__TIMER);
		} else if (evtparams['event'] == 'invoiceasset.onmouseout' ) {
			setTimeout(function () {
				TOOLTIP._setStatus(module, fieldname, recordid, false);
				TOOLTIP.hide(node, recordid, fieldname);
				clearTimeout(_VT__TOOLTIP__TIMER);
			}, TOOLTIP._mouseOverTimeOut*4);
		}
	},

	_showForField : function (node, module, fieldname, recordid) {
		if (!this._relinguishStatusControl) {
			document.getElementById('status').style.display = 'block';
		}
		if (typeof(TOOLTIP._cache[module]) == 'undefined') {
			TOOLTIP._cache[module] = {};
		}
		if (!TOOLTIP._cache[module][fieldname] && TOOLTIP._cache[module][fieldname] !== undefined) {
			if (!this._relinguishStatusControl) {
				document.getElementById('status').style.display = 'none';
			}
			return;
		}

		if (typeof(TOOLTIP._cache[module][fieldname]) == 'undefined') {
			TOOLTIP._cache[module][fieldname] = {};
		}

		if (typeof(TOOLTIP._cache[module][fieldname][recordid]) == 'undefined') {
			// Cache miss
			TOOLTIP._cache[module][fieldname][recordid] = false;
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Tooltip&action=TooltipAjax&file=ComputeTooltip&fieldname='+fieldname+'&id='+recordid+'&modname='+module+'&ajax=true&submode=getTooltip'
			}).done(function (response) {
				var data = response;
				if (data) {
					TOOLTIP._cache[module][fieldname][recordid] = data;
					TOOLTIP.show(node, module, fieldname, recordid);
					if (!this._relinguishStatusControl) {
						document.getElementById('status').style.display = 'none';
					}
				} else {
					TOOLTIP._cache[module][fieldname] = false;
					if (!this._relinguishStatusControl) {
						document.getElementById('status').style.display = 'none';
					}
				}
			});
		} else {
			// Cache hit
			TOOLTIP.show(node, module, fieldname, recordid);
		}
	}
};

vtlib_listview.register('cell.onmouseover', TOOLTIP.handler);
vtlib_listview.register('cell.onmouseout', TOOLTIP.handler);
vtlib_listview.register('invoiceasset.onmouseout', TOOLTIP.handler);
