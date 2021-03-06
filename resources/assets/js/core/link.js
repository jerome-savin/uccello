export class Link {
    constructor(initListeners) {
        if (initListeners !== false) {
            this.initListeners()
        }
    }

    initListeners() {
        this.initClickListener()
    }

    initClickListener(parentElement) {
        if (typeof parentElement === 'undefined') {
            parentElement = null
        }

        $("a[data-config], button[data-config]", parentElement).on('click', (event) => {

            event.preventDefault()
            event.stopImmediatePropagation()

            this.element = $(event.currentTarget)
            this.config = this.element.data('config')

            // Remove focus
            this.element.blur()

            if (this.config === null) {
                this.config = {}
            }

            // Check if we need a confirmation or if we laucnh action directly
            if (this.config.confirm === true) {
                this.confirm()
            } else {
                this.launchAction()
            }
        })
    }

    /**
     * Launch action according to its type
     */
    launchAction() {
        switch (this.config.actionType) {
            // Simple link
            case 'link' :
                    this.gotoUrl()
                break

            // Ajax call
            case 'ajax':
                    this.callUrl()
                break
        }
    }

    /**
     * Go to URL according to the target if it is defined
     */
    gotoUrl() {
        // Get target if defined, or use default one
        let target = this.config.target ? this.config.target : '_self'

        // Go to the link
        window.open(this.element.attr('href'), target)
    }

    /**
     * Call URL by Ajax
     */
    callUrl() {
        // Get URL
        let url = this.element.attr('href')

        // Get Ajax config
        let ajaxConfig = typeof this.config.ajax === 'object' ? this.config.ajax : {}

        // Method
        let method = "get"
        if (ajaxConfig.method) { method =  ajaxConfig.method}

        // Params
        let params = {}
        if (ajaxConfig.params) { params =  ajaxConfig.params}

        // If POST is used, add CSRF token to parameters
        if (method === 'post') {
            const csrfToken = $('meta[name="csrf-token"]').attr('content')
            params['_token'] = csrfToken
        }

        // Element to update with the call response
        let elementToUpdate = null
        if (ajaxConfig.elementToUpdate) { elementToUpdate =  ajaxConfig.elementToUpdate}

        // Call
        $.ajax({
            url: url,
            method: method,
            data: params,
        })
        .then((response) => {

            // If we want to update an element in the page, do it
            if (elementToUpdate) {
                $(elementToUpdate).html(response.content)
            }
            // Display dialog displaying success or error
            else {
                if (response.success === false) {
                    swal(uctrans.trans('uccello::default.dialog.error.title'), response.message, "error")
                } else {
                    swal(uctrans.trans('uccello::default.dialog.success.title'), response.message, "success")

                    // Reload page if needed
                    if (ajaxConfig.refresh === true) {
                        document.location.reload()
                    }
                }
            }
        })
        // Impossible to reach the URL. Display error
        .catch((error) => {
            swal(uctrans.trans('uccello::default.dialog.error.title'), error.message, "error")
        })
    }

    /**
     * Show confirm dialog
     */
    confirm() {
        // Get config
        let title, text, confirmButtonText, confirmButtonColor, closeOnConfirm

        if (typeof this.config.dialog === 'object') {
            title = this.config.dialog.title
            text = this.config.dialog.text
            confirmButtonText = this.config.dialog.confirmButtonText
            confirmButtonColor = this.config.dialog.confirmButtonColor
            closeOnConfirm = this.config.dialog.closeOnConfirm
        }

        // Default config
        if (!title) { title = uctrans.trans('uccello::default.confirm.dialog.title') }
        if (!confirmButtonText) { confirmButtonText = uctrans.trans('uccello::default.button.yes') }
        if (!confirmButtonColor) { confirmButtonColor = '#DD6B55' }
        if (!closeOnConfirm) { closeOnConfirm = true }

        // If it is an AJAX call, show loader
        let showLoaderOnConfirm = false
        if (this.config.actionType === 'ajax') {
            showLoaderOnConfirm = true

            if (!this.config.ajax || !this.config.ajax.elementToUpdate) {
                closeOnConfirm = false
            }
        }

        // Show confirm dialog
        swal({
            title: title,
            text: text,
            icon: "warning",
            buttons: {
                cancel: {
                  text: uctrans.trans('uccello::default.button.no'),
                  value: null,
                  visible: true,
                },
                confirm: {
                  text: confirmButtonText,
                  value: true,
                  className: "red",
                  closeModal: closeOnConfirm
                }
            }
        }).then((response) => {
            if (response === true) {
                this.launchAction()
            }
        })
    }
}