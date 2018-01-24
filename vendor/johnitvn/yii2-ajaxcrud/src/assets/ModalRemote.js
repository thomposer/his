/*!
 * Modal Remote
 * =================================
 * Use for johnitvn/yii2-ajaxcrud extension
 * @author John Martin john.itvn@gmail.com
 */
(function ($) {
    $.fn.hasAttr = function (name) {
        return this.attr(name) !== undefined;
    };
}(jQuery));


function ModalRemote(modalId) {

    this.defaults = {
        okLabel: "确定",
        executeLabel: "Execute",
        cancelLabel: "取消",
        loadingTitle: "Loading"
    };

    this.modal = $(modalId);

    this.dialog = $(modalId).find('.modal-dialog');

    this.header = $(modalId).find('.modal-header');

    this.content = $(modalId).find('.modal-body');

    this.footer = $(modalId).find('.modal-footer');

    this.loadingContent = '<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>';


    /**
     * Show the modal
     */
    this.show = function () {
        this.clear();
        $(this.modal).modal('show');
    };

    /**
     * Hide the modal
     */
    this.hide = function () {
        $(this.modal).modal('hide');
    };

    /**
     * Toogle show/hide modal
     */
    this.toggle = function () {
        $(this.modal).modal('toggle');
    };

    /**
     * Clear modal
     */
    this.clear = function () {
        $(this.modal).find('.modal-title').remove();
        $(this.content).html("");
        $(this.footer).html("");
    };

    /**
     * Set size of modal
     * @param {string} size large/normal/small
     */
    this.setSize = function (size) {
        $(this.dialog).removeClass('modal-lg');
        $(this.dialog).removeClass('modal-sm');
        if (size == 'large')
            $(this.dialog).addClass('modal-lg');
        else if (size == 'small')
            $(this.dialog).addClass('modal-sm');
        else if (size !== 'normal')
            console.warn("Undefined size " + size);
    };

    /**
     * Set modal header
     * @param {string} content The content of modal header
     */
    this.setHeader = function (content) {
        $(this.header).html(content);
    };

    /**
     * Set modal content
     * @param {string} content The content of modal content
     */
    this.setContent = function (content) {
        $(this.content).html(content);
    };

    /**
     * Set modal footer
     * @param {string} content The content of modal footer
     */
    this.setFooter = function (content) {
        $(this.footer).html('<div class="form-group">' + content + '</div>');
    };

    /**
     * Set modal footer
     * @param {string} title The title of modal
     */
    this.setTitle = function (title) {
        // remove old title
        $(this.header).find('h4.modal-title').remove();
        // add new title
        $(this.header).append('<h4 class="modal-title">' + title + '</h4>');
    };

    /**
     * Hide close button
     */
    this.hidenCloseButton = function () {
        $(this.header).find('button.close').hide();
    };

    /**
     * Show close button
     */
    this.showCloseButton = function () {
        $(this.header).find('button.close').show();
    };

    /**
     * Show loading state in modal
     */
    this.displayLoading = function () {
        this.setContent(this.loadingContent);
        this.setTitle(this.defaults.loadingTitle);
    };

    /**
     * Add button to footer
     * @param string label The label of button
     * @param string classes The class of button
     * @param callable callback the callback when button click
     */
    this.addFooterButton = function (label, type, classes, callback) {
        buttonElm = document.createElement('button');
        buttonElm.setAttribute('type', type === null ? 'button' : type);
        buttonElm.setAttribute('class', classes === null ? 'btn btn-default btn-form' : classes);
        buttonElm.innerHTML = label;
        var instance = this;
        $(this.footer).append(buttonElm);
        if (callback !== null) {
            $(buttonElm).click(function (event) {
                callback.call(instance, this, event);
            });
        }
    };

    /**
     * Send ajax request and wraper response to modal
     * @param {string} url The url of request
     * @param {string} method The method of request
     * @param {object}data of request
     */
    this.doRemote = function (url, method, data) {
        var instance = this;
        var contentTypeStr = arguments[3] ? arguments[3] : false;
        var processDataStr = arguments[4] ? true : false;
        $.ajax({
            url: url,
            method: method,
            data: data,
            async: false,
            beforeSend: function () {
                beforeRemoteRequest.call(instance);
            },
            error: function (response) {
                errorRemoteResponse.call(instance, response);
            },
            success: function (response) {
                successRemoteResponse.call(instance, response);
            },
            contentType: contentTypeStr,
            cache: false,
            processData: processDataStr
        });
    };

    /**
     * Before send request process
     * - Ensure clear and show modal
     * - Show loading state in modal
     */
    function beforeRemoteRequest() {
        this.show();
        this.displayLoading();
    }


    /**
     * When remote sends error response
     * @param {string} response
     */
    function errorRemoteResponse(response) {
        this.setTitle(response.status + response.statusText);
        this.setContent(response.responseText);
        this.addFooterButton('取消', 'button', 'btn btn-cancel btn-form search-form', function (button, event) {
            this.hide();
        })
    }

    /**
     * When remote sends success response
     * @param {string} response
     */
    function successRemoteResponse(response) {

        // Reload datatable if response contain forceReload field
        //局部刷新内容部分
        if (response.forceReload !== undefined && response.forceReload) {
            if (response.forceReload == 'true') {
                // Backwards compatible reload of fixed crud-datatable-pjax
                $.pjax.reload({container: '#crud-datatable-pjax',cache:false,timeout : 5000});
            } else {
                $.pjax.reload({container: response.forceReload,cache:false,timeout : 5000});
            }
        }
        //重定向url
        if (response.forceRedirect !== undefined && response.forceRedirect) {
            if (response.forceRedirect == 'true') {
                window.history.back();
            } else {
                window.location.href = response.forceRedirect;
            }
        }
        if (response.forceReloadPage !== undefined && response.forceReloadPage) {
            window.location.reload();
        }
        //提示信息
        if (response.forceMessage !== undefined && response.forceMessage) {
            if (response.forceMessage == 'true') {
                showInfo('保存成功', '180px');
            } else {
                if (response.forceType !== undefined && response.forceType == 2) {
                    showInfo(response.forceMessage, '250px', 2);//错误提示
                    //若需自动关闭
                    if (response.forceAutoClose !== undefined && response.forceAutoClose) {
                        setTimeout(this.hide(), 1000);
                        return;
                    }
                } else {
                    showInfo(response.forceMessage, '250px');
                }
            }
        }
        //回调方法
        if (response.forceCallback !== undefined && response.forceCallback) {
            eval(response.forceCallback);
        }
        // Close modal if response contains forceClose field
        if (response.forceClose !== undefined && response.forceClose) {
            this.hide();
            return;
        }
        console.log(response,'response');
        if (!response.reset) {
            console.log(response.reset,'response.reset');
            if (response.size !== undefined)
                this.setSize(response.size);

            if (response.title !== undefined)
                this.setTitle(response.title);

            if (response.content !== undefined)
                this.setContent(response.content);

            if (response.footer !== undefined)
                this.setFooter(response.footer);
        }
        if ($(this.content).find("form")[0] !== undefined) {
            this.setupFormSubmit(
                    $(this.content).find("form")[0],
                    $(this.footer).find('[type="submit"]')[0]
                    );
        }
    }

    /**
     * Prepare submit button when modal has form
     * @param {string} modalForm
     * @param {object} modalFormSubmitBtn
     */
    this.setupFormSubmit = function (modalForm, modalFormSubmitBtn) {

        if (modalFormSubmitBtn === undefined) {
            // If submit button not found throw warning message
            console.warn('Modal has form but does not have a submit button');
        } else {
            var instance = this;

            // Submit form when user clicks submit button
            $(modalFormSubmitBtn).click(function (e) {
                var data;

                // Test if browser supports FormData which handles uploads
                if (window.FormData) {
                    data = new FormData($(modalForm)[0]);
                } else {
                    // Fallback to serialize
                    data = $(modalForm).serializeArray();
                }

                instance.doRemote(
                        $(modalForm).attr('action'),
                        $(modalForm).hasAttr('method') ? $(modalForm).attr('method') : 'GET',
                        data,
                        $(modalForm).hasAttr('contentType') ? $(modalForm).attr('contentType') : false
                        );
            });
        }
    };

    /**
     * Show the confirm dialog
     * @param {string} class of left button
     * @param {string} class of right button
     * @param {string} title The title of modal
     * @param {string} message The message for ask user
     * @param {string} okLabel The label of ok button
     * @param {string} cancelLabel The class of cancel button
     * @param {string} size The size of the modal
     * @param {string} dataUrl Where to post
     * @param {string} dataRequestMethod POST or GET
     * @param {number[]} selectedIds
     */
    this.confirmModal = function (leftClass, rightClass, title, message, okLabel, cancelLabel, type, size, dataUrl, dataRequestMethod, selectedIds) {
        this.show();
        this.setSize(size);

        if (title !== undefined) {
            this.setTitle(title);
        }
        // Add form for user input if required
        this.setContent('<form id="ModalRemoteConfirmForm">' + message);

        var instance = this;
        if (!type) {
            this.addFooterButton(
                    cancelLabel === undefined ? this.defaults.cancelLabel : cancelLabel,
                    'button',
                    'btn btn-cancel btn-form ' + (rightClass === undefined ? '' : rightClass),
                    function (e) {
                        this.hide();
                    }
            );
            this.addFooterButton(
                    okLabel === undefined ? this.defaults.okLabel : okLabel,
                    'submit',
                    'btn btn-default btn-form ' + (leftClass === undefined ? '' : leftClass),
                    function (e) {
                        var data;

                        // Test if browser supports FormData which handles uploads
                        if (window.FormData) {
                            data = new FormData($('#ModalRemoteConfirmForm')[0]);
                            if (typeof selectedIds !== 'undefined' && selectedIds)
                                data.append('pks', selectedIds.join());
                        } else {
                            // Fallback to serialize
                            data = $('#ModalRemoteConfirmForm');
                            if (typeof selectedIds !== 'undefined' && selectedIds)
                                data.pks = selectedIds;
                            data = data.serializeArray();
                        }

                        instance.doRemote(
                                dataUrl,
                                dataRequestMethod,
                                data
                                );
                    }
            );
        } else {
            this.addFooterButton(
                    cancelLabel === undefined ? this.defaults.cancelLabel : cancelLabel,
                    'button',
                    'btn  btn-form ' + (leftClass === undefined ? 'btn-default' : leftClass),
                    function (e) {
                        this.hide();
                    }
            );
            this.addFooterButton(
                    okLabel === undefined ? this.defaults.okLabel : okLabel,
                    'submit',
                    'btn btn-form ' + (rightClass === undefined ? 'btn-cancel' : rightClass),
                    function (e) {
                        var data;

                        // Test if browser supports FormData which handles uploads
                        if (window.FormData) {
                            data = new FormData($('#ModalRemoteConfirmForm')[0]);
                            if (typeof selectedIds !== 'undefined' && selectedIds)
                                data.append('pks', selectedIds.join());
                        } else {
                            // Fallback to serialize
                            data = $('#ModalRemoteConfirmForm');
                            if (typeof selectedIds !== 'undefined' && selectedIds)
                                data.pks = selectedIds;
                            data = data.serializeArray();
                        }

                        instance.doRemote(
                                dataUrl,
                                dataRequestMethod,
                                data
                                );
                    }
            );

        }


    }

    /**
     * Open the modal
     * HTML data attributes for use in local confirm
     *   - href/data-url         (If href not set will get data-url)
     *   - data-request-method   (string GET/POST)
     *   - data-confirm-ok       (string OK button text)
     *   - data-confirm-cancel   (string cancel button text)
     *   - data-confirm-title    (string title of modal box)
     *   - data-confirm-message  (string message in modal box)
     *   - data-modal-size       (string small/normal/large)
     * Attributes for remote response (json)
     *   - forceReload           (string reloads a pjax ID)
     *   - forceClose            (boolean remote close modal)
     *   - size                  (string small/normal/large)
     *   - title                 (string/html title of modal box)
     *   - content               (string/html content in modal box)
     *   - footer                (string/html footer of modal box)
     * @params {elm}
     */
    this.open = function (elm, bulkData) {
        if ($(elm).hasAttr('data-modal-size')) {
            this.setSize($(elm).attr('data-modal-size'));
        }
        /**
         * Show either a local confirm modal or get modal content through ajax
         */
        if ($(elm).hasAttr('data-confirm-title') || $(elm).hasAttr('data-confirm-message')) {
            this.confirmModal(
                    $(elm).attr('left-class'),
                    $(elm).attr('right-class'),
                    $(elm).attr('data-confirm-title'),
                    $(elm).attr('data-confirm-message'),
                    $(elm).attr('data-confirm-ok'),
                    $(elm).attr('data-confirm-cancel'),
                    $(elm).hasAttr('data-delete') ? $(elm).attr('data-delete') : true,
                    $(elm).hasAttr('data-modal-size') ? $(elm).attr('data-modal-size') : 'normal',
                    $(elm).hasAttr('href') ? $(elm).attr('href') : $(elm).attr('data-url'),
                    $(elm).hasAttr('data-request-method') ? $(elm).attr('data-request-method') : 'GET',
                    bulkData
                    )
        } else {
            this.doRemote(
                    ($(elm).hasAttr('href') && $(elm).attr('href') != '#') ? $(elm).attr('href') : $(elm).attr('data-url'),
                    $(elm).hasAttr('data-request-method') ? $(elm).attr('data-request-method') : 'GET',
                    bulkData,
                    $(elm).hasAttr('contentType') ? $(elm).attr('contentType') : false,
                    $(elm).hasAttr('processData') ? $(elm).attr('processData') : false
                    );
        }
    }
} // End of Object
