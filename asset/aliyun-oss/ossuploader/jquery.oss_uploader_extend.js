;(function ($) {
	var conf = {
		preventUpload: {
			order: 1,
			invoke: function(option){
				var $form = $(this).parents('form');
				var $submitBtn = $form.find('button[type=submit]');
				var uploadCountField = 'oss-submit-disabled-times';
				if(!$submitBtn.length || !$form.length){
					return false;
				}
				var submitBtnTxt = $submitBtn.text();
				
				var preventSubmit = function(){
					var tips = '图片上传中';
					option.show_msg(tips);
					return false;
				};
				
				var offSubmit = function () {
					if($submitBtn.data('oss-submit-disabled')){
						$submitBtn.attr({
							disabled: false,
						});
					}
					$submitBtn.text(submitBtnTxt).data('oss-submit-disabled','');
					$form.off('submit',preventSubmit);
					return true;
				};
				
				var getDisabledTimes = function () {
					var r = parseInt($submitBtn.data(uploadCountField)) || 0;
					return Math.max(r, 0);
				};
				
				var handleFinish = function (option) {
					var disabledTimes = getDisabledTimes();
					disabledTimes--;
					disabledTimes = Math.max(disabledTimes, 0);
					$submitBtn.data(uploadCountField, disabledTimes);
					if(disabledTimes <= 0){
						return offSubmit();
					}
					return true;
				};
				
				return {
					beforeUpload: function () {
						//判断是否本来已经disabled
						if(!$submitBtn.attr('disabled')){
							$submitBtn.data('oss-submit-disabled', true);
						}
						$submitBtn.attr({
							disabled: true,
						}).text('上传中');
						
						var disabledTimes = getDisabledTimes();
						disabledTimes++;
						$submitBtn.data(uploadCountField, disabledTimes);
						
						$form.on('submit',preventSubmit);
						return true;
					},
					uploadError: handleFinish,
					filePerUploaded: handleFinish,
					deleteFile: function (option, isUploadComplete) {
						if(isUploadComplete === false){
							handleFinish();
							var disabledTimes = getDisabledTimes();
							disabledTimes++;
							$submitBtn.data(uploadCountField,disabledTimes);
						}
						return true;
					},
				};
			}
		},
	};
	$.fn.ossuploaderWrapper = function (option, extend) {
		extend = Array.isArray(extend) ? extend : ['preventUpload'];
		var that = this;
		
		var cbFn = {};
		//设置默认order
		for(var i in conf){
			conf[i].order = conf[i].order || 2;
		}
		//拓展排序
		extend.sort(function (i,j) {
			return conf[j].order - conf[i].order;
		});
		//分发拓展功能
		extend.forEach( function(item) {
			var confItem = conf[item];
			var invoke = typeof confItem.invoke === 'function' ?
				confItem.invoke.call(that)
				: confItem.invoke;
			if(!invoke){
				return false;
			}
			for(var i in invoke){
				!cbFn[i] && (cbFn[i] = []);
				cbFn[i].push(invoke[i]);
			}
		});
		
		var runCallbackQueue = function (cbArr) {
			return function () {
				var args = [].slice.call(arguments);
				args.unshift(option);
				for(var j = 0; j < cbArr.length;j++){
					if(cbArr[j].apply(that, args) === false){
						return false;
					}
				}
			}
		};
		
		//重置回调操作
		for(var i in cbFn){
			var cb = option[i] || function () {return true;};
			var item = cbFn[i];
			item.unshift(cb);
			option[i] = runCallbackQueue(item);
		}
		
		return $(this).ossuploader(option);
	};
})(jQuery);
