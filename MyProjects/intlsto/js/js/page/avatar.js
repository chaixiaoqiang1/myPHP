(function(factory) {
    if(typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if(typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
})(function($) {

    'use strict';

    var console = window.console || {
            log: function() {}
        };

    function CropAvatar($element) {
        this.$container = $element;

        this.$avatarView = this.$container.find('.avatar-view');
        this.$avatar = this.$avatarView.find('img');
        this.$avatarModal = $("body").find('#photoBody');
        this.$loading = $("#page-wrapper").find('.loading');

        this.$avatarForm = this.$avatarModal.find('.avatar-form');
        this.$avatarUpload = this.$avatarForm.find('.avatar-upload');
        this.$avatarSrc = this.$avatarForm.find('.avatar-src');
        this.$avatarData = this.$avatarForm.find('.avatar-data');
        this.$avatarInput = this.$avatarForm.find('.avatar-input');
        this.$avatarSave = this.$avatarForm.find('.avatar-save');
        this.$avatarBtns = this.$avatarForm.find('.avatar-btns');

        this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
        this.$avatarPreview = this.$avatarModal.find('.avatar-preview');

        this.init();
    }

    CropAvatar.prototype = {
        constructor: CropAvatar,
        support: {
            fileList: !!$('<input type="file">').prop('files'),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },

        init: function() {
            this.support.datauri = this.support.fileList && this.support.blobURLs;

            if(!this.support.formData) {
                this.initIframe();
            }

            this.initTooltip();
            this.initModal();
            this.addListener();
        },

        addListener: function() {
            this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
        },

        initTooltip: function() {
            this.$avatarView.tooltip({
                placement: 'bottom'
            });
        },

        initModal: function() {
            this.$avatarModal.modal({
                show: false
            });
        },

        initPreview: function() {
            var url = this.$avatar.attr('src');

//			this.$avatarPreview.empty().html('<img src="' + url + '">');
        },

        initIframe: function() {
            var target = 'upload-iframe-' + (new Date()).getTime(),
                $iframe = $('<iframe>').attr({
                    name: target,
                    src: ''
                }),
                _this = this;

            // Ready ifrmae
            $iframe.one('load', function() {

                // respond response
                $iframe.on('load', function() {
                    var data;

                    try {
                        data = $(this).contents().find('body').text();
                    } catch(e) {
                        console.log(e.message);
                    }

                    if(data) {
                        try {
                            data = $.parseJSON(data);
                        } catch(e) {
                            console.log(e.message);
                        }

                        _this.submitDone(data);
                    } else {
                        _this.submitFail('Image upload failed!');
                    }

                    _this.submitEnd();

                });
            });

            this.$iframe = $iframe;
            this.$avatarForm.attr('target', target).after($iframe.hide());
        },

        click: function() {
            this.$avatarModal.modal('show');
            this.initPreview();
        },

        change: function() {
            var files,
                file;

            if(this.support.datauri) {
                files = this.$avatarInput.prop('files');

                if(files.length > 0) {
                    file = files[0];

                    if(this.isImageFile(file)) {
                        if(this.url) {
                            URL.revokeObjectURL(this.url); // Revoke the old one
                        }

                        this.url = URL.createObjectURL(file);
                        this.startCropper();
                    }
                }
            } else {
                file = this.$avatarInput.val();

                if(this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },

        submit: function() {
            if(!this.$avatarSrc.val() && !this.$avatarInput.val()) {
                return false;
            }

            if(this.support.formData) {
                this.ajaxUpload();
                return false;
            }
        },

        rotate: function(e) {
            var data;

            if(this.active) {
                data = $(e.target).data();

                if(data.method) {
                    this.$img.cropper(data.method, data.option);
                }
            }
        },

        isImageFile: function(file) {
            if(file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },

        startCropper: function() {
            var _this = this;

            if(this.active) {
                this.$img.cropper('replace', this.url);
            } else {
                this.$img = $('<img src="' + this.url + '">');
                this.$avatarWrapper.empty().html(this.$img);
                this.$img.cropper({
                    aspectRatio: 1,  //宽高比例
                    preview: this.$avatarPreview.selector,
                    strict: false,
//					crop: function(data) {
//						var json = [
//							'{"x":' + data.x,
//							'"y":' + data.y,
//							'"height":' + data.height,
//							'"width":' + data.width,
//							'"rotate":' + data.rotate + '}'
//						].join();
//						_this.$avatarData.val(json);
//					}
                });

                this.active = true;
            }
        },

        stopCropper: function() {
            if(this.active) {
                this.$img.cropper('destroy');
                this.$img.remove();
                this.active = false;
            }
        },

//		ajaxUpload: function() {
//			var url = this.$avatarForm.attr('action'),
//				data = new FormData(this.$avatarForm[0]),
//				_this = this;
//
//			$.ajax(url, {
//				headers: {
//					'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//				},
//				type: 'post',
//				data: data,
//				dataType: 'json',
//				processData: false,
//				contentType: false,
//
//				beforeSend: function() {
//					_this.submitStart();
//				},
//
//				success: function(data) {
//					_this.submitDone(data);
//				},
//
//				error: function(XMLHttpRequest, textStatus, errorThrown) {
//					if (this.uploaded) {
//					    this.uploaded = false;
//					    this.cropDone(); 
//					    // this.uploaded = true;this.support.datauri ||           
//					    // this.$avatarSrc.val(this.url);            
//					    // this.startCropper();         
//					 } else {           
//					     this.uploaded = true;            
//					     this.$avatarSrc.val(this.url);           
//					     this.startCropper();            
//					     this.cropDone();          
//					}
//				},
//
//				compvare: function() {
//					_this.submitEnd();
//				}
//			});
//		},

        syncUpload: function() {
            this.$avatarSave.click();
        },

        submitStart: function() {
            this.$loading.fadeIn();
        },

//		submitDone: function(data) {
//			if($.isPlainObject(data)) {
//				if(data.result) {
//					this.url = data.result;
//					if(this.support.datauri || this.uploaded) {
//						this.uploaded = false;
//						this.cropDone();
//					} else {
//						this.uploaded = true;
//						this.$avatarSrc.val(this.url);
//						this.startCropper();
//					}
//					this.$avatarInput.val('');
//				} else if(data.message) {
//					this.alert(data.message);
//				}
//			} else {
//				this.alert('Failed to response');
//			}
//		},

        submitFail: function(msg) {
            this.alert(msg);
        },

        submitEnd: function() {
            this.$loading.fadeOut();
        },

        cropDone: function() {
            this.$avatarForm.get(0).reset();
            this.$avatar.attr('src', this.url);
            this.stopCropper();
            this.$avatarModal.modal('hide');
        },

        alert: function(msg) {
            var $alert = [
                '<div class="alert alert-danger avater-alert">',
                '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                msg,
                '</div>'
            ].join('');

            this.$avatarUpload.after($alert);
        }
    };

    $(function() {
        return new CropAvatar($('#crop-avatar'));
    });

});

//上面为调用插件js,下面为页面特效js
$(function () {
    //做个下简易的验证  大小 格式
    $('#avatarInput').on('change', function(e) {
        var filemaxsize = 1024 * 2,//2M
            target = $(e.target),
            Size = target[0].files[0].size / 1024;
        if(Size > filemaxsize) {
            layer.msg('图片过大，请重新选择!');
            $(".avatar-wrapper").children().remove;
            return false;
        }
        if(!this.files[0].type.match(/image.*/)) {
            layer.msg('请选择正确的图片!')
        } else {
            $(".row").removeClass('hide');
            /*var filename = document.querySelector("#avatar-name");
             var texts = document.querySelector("#avatarInput").value;
             var teststr = texts; //你这里的路径写错了
             testend = teststr.match(/[^\\]+\.[^\(]+/i); //直接完整文件名的
             filename.innerHTML = testend;*/
        }

    });

    $("#saveChange").on("click", function() {
        var img_lg = document.getElementById('imageHead');
        //判断是否含有img
        if(!img_lg.querySelector('img')){
            alert("请先上传头像图片");
            return false;
        }
        // 截图小的显示框内的内容
        html2canvas(img_lg,{
            allowTaint: true,
            taintTest: false,
            onrendered: function(canvas) {
                canvas.id = "mycanvas";
                //生成base64图片数据
                var dataUrl = canvas.toDataURL("image/jpeg"),
                    newImg = document.createElement("img");
                newImg.src = dataUrl;
                imagesAjax(dataUrl)
            }
        });
    });
});

/**
 * 头像上传
 * @param src
 */
function imagesAjax(src) {
    $.ajax({
        url: avatar_url,
        data: {img:src},
        type: "POST",
        dataType: 'json',
        success: function(data) {
            if(data.state){
                layer.msg(data.message,{end:function () {
                    $("#avatarImg",parent.document).attr('src',src);
                    $(".user_box>img",parent.parent.document).attr('src',src);
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                }});

            }else {
                layer.msg(data.message);
            }
        }
    });
}