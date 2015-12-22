Element.prototype.triggerEvent = function (b) {
    if (document.createEvent) {
        var a = document.createEvent("HTMLEvents");
        a.initEvent(b, true, true);
        return this.dispatchEvent(a)
    }
    if (this.fireEvent) {
        return this.fireEvent("on" + b)
    }
};
Object.extend(Prototype.Browser, {
    ie6: (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ? (Number(RegExp.$1) == 6 ? true : false) : false,
    ie7: (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ? (Number(RegExp.$1) == 7 ? true : false) : false,
    ie8: (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ? (Number(RegExp.$1) == 8 ? true : false) : false,
    ie9: (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ? (Number(RegExp.$1) == 9 ? true : false) : false,
    ie10: (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ? (Number(RegExp.$1) == 10 ? true : false) : false
});
var IdeCheckoutvm = Class.create();
IdeCheckoutvm.prototype = {
    _beforeRedirectClientFunc: $H({}), initialize: function (b, a) {
        this.form = b;
        this.saveUrl = a.saveUrl;
        this.billingUrl = a.billingUrl;
        this.successUrl = a.successUrl;
        this.failureUrl = a.failureUrl;
        this.ajaxLoaderUrl = a.ajaxLoaderUrl;
        this.submitOrderText = a.submitOrderText;
        this.conditionTermsText = a.conditionTermsText;
        this.billingForm = false;
        this.shippingForm = false;
        this.syncBillingShipping = false;
        this.method = "";
        this.payment = "";
        this.buttonCheckoutvm = new IdeButtonCheckoutvm({
            ajaxLoaderUrl: this.ajaxLoaderUrl,
            submitOrderText: this.submitOrderText
        });
        this.agreementsForm = false;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this._addEvents()
    }, addBeforeRedirectClient: function (b, a) {
        this._beforeRedirectClientFunc.set(b, a)
    }, _beforeRedirectClient: function () {
        (this._beforeRedirectClientFunc).each(function (a) {
            (a.value)()
        })
    }, resetBeforeRedirectClient: function () {
        this._beforeRedirectClientFunc = $H({})
    }, getBillingUrl: function () {
        return this.billingUrl
    }, _addEvents: function () {
    }, ajaxFailure: function () {
        this.buttonCheckoutvm.enable()
    }, setLoadWaiting: function (b) {
        if (b) {
            var a = $("checkout-" + b + "-load");
            if (a) {
                a.update('<div class="please-wait"></div>')
            }
        }
    }, setStepResponse: function (a) {
        if (a.updateSection) {
            this.updateBlock(a.updateSection.shippingMethod);
            this.updateBlock(a.updateSection.paymentMethod);
            this.updateBlock(a.updateSection.review)
        }
        if (a.allow_sections) {
            a.allow_sections.each(function (b) {
                $("opc-" + b).addClassName("allow")
            })
        }
        if (a.duplicateBillingInfo) {
            shipping.setSameAsBilling(true, false)
        }
        if (a.redirectUrl) {
            location.href = a.redirectUrl
        }
        return false
    }, updateBlock: function (a) {
        if (a != null) {
            if ($("checkout-" + a.name + "-load")) {
                $("checkout-" + a.name + "-load").update(a.html)
            }
        }
    }, save: function () {
        var a = new Validation(this.form);
        if (!a.validate()) {
            return false
        }
        try {
            if (shippingMethod) {
                if (!shippingMethod.validate()) {
                    return false
                }
            }
        } catch (c) {
        }
        if (!payment.validate()) {
            return false
        }
        a.reset();
        this.buttonCheckoutvm.disable();
        var d = Form.serialize(this.form);
        var b = new Ajax.Request(payment.updatePaymentUrl, {
            asynchronous: true,
            method: "post",
            parameters: d,
            onSuccess: this.process.bindAsEventListener(this)
        })
    }, process: function () {
        var b = Form.serialize(this.form);
        if (this.agreementsForm) {
            b += "&" + Form.serialize(this.agreementsForm)
        }
        b.save = true;
        var a = new Ajax.Request(this.saveUrl, {
            method: "post",
            parameters: b,
            onComplete: this.onComplete,
            onSuccess: this.onSave,
            onFailure: checkout.ajaxFailure.bind(checkout)
        })
    }, nextStep: function (transport) {
        var response = null;
        if (transport && transport.responseText) {
            try {
                response = eval("(" + transport.responseText + ")")
            } catch (e) {
                response = {}
            }
        }
        if (response.redirectUrl) {
            this._beforeRedirectClient();
            this.isSuccess = true;
            if (response.redirectUrl != "payment_same_screen") {
                location.href = response.redirectUrl;
                return
            }
        }
        if (response.error) {
            this.buttonCheckoutvm.enable();
            if (response.errorMessage.message) {
                this._showErrorMessage(response.errorMessage.message)
            } else {
                this._clearMessage()
            }
            if (response.errorMessage.errorFields) {
                var ideCheckoutValidationInst = new IdeCheckoutvmValidation(this.form);
                ideCheckoutValidationInst.fillFormErrors(response.errorMessage.errorFields)
            }
        }
    }, _showErrorMessage: function (b) {
        var a = '<div id="messages"><ul class="messages"><li class="error-msg"><ul><li><span>' + b + "</span></li></ul></li></ul></div>";
        $("idecheckoutvm-checkout-message").update(a)
    }, _showNoteMessage: function (b) {
        var a = '<div id="messages"><ul class="messages"><li class="note-msg"><ul><li><span>' + b + "</span></li></ul></li></ul></div>";
        $("idecheckoutvm-checkout-message").update(a)
    }, _clearMessage: function () {
        $("idecheckoutvm-checkout-message").update("")
    }, isSuccess: false
};
var IdeButtonCheckoutvm = Class.create();
IdeButtonCheckoutvm.prototype = {
    initialize: function (a) {
        this.ajaxLoaderUrl = a.ajaxLoaderUrl;
        this.submitOrderText = a.submitOrderText;
        this.buttonId = "idecheckoutvm-place-order-button";
        this.loadingId = "idecheckoutvm-loading-button";
        this._addEvents()
    }, _addEvents: function () {
    }, disable: function () {
        var a = $(this.buttonId);
        var c = $(this.loadingId);
        if (!c) {
            var b = '<img src="' + this.ajaxLoaderUrl + '" />&nbsp;&nbsp;' + this.submitOrderText;
            c = new Element("div").writeAttribute("id", this.loadingId).addClassName("loading").update(b);
            a.parentNode.appendChild(c)
        } else {
            c.show()
        }
        a.removeClassName("enable").addClassName("disable");
        a.disabled = true
    }, enable: function () {
        var a = $(this.buttonId);
        var b = $(this.loadingId);
        if (b) {
            b.hide()
        }
        a.removeClassName("disable").addClassName("enable");
        a.disabled = false
    }
};
var IdeBilling = Class.create();
IdeBilling.prototype = {
    initialize: function (a) {
        this.telephone = "billing:telephone";
        this.mobile = "billing:mobile";
        this.region = "billing:region";
        this.regionId = "billing:region_id";
        this.customerPassword = "billing:customer_password";
        this.addressSelect = "billing-address-select";
        this.registerCustomer = "register-customer";
        this.sameAsBilling = "shipping:same_as_billing";
        this.useForShipping = "billing:use_for_shipping";
        this.addressUrl = a.addressUrl;
        this.checkEmailUrl = a.checkEmailUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);
        this.onAfterUpdate = this.nextStep.bindAsEventListener(this);
        this._addEvents()
    }, _addEvents: function () {
        if ($(this.regionId)) {
            $(this.regionId).observe("change", function (a) {
                if ($(shipping.sameAsBilling) && $(shipping.sameAsBilling).checked) {
                    shipping.syncWithBilling()
                }
                this.update()
            }.bind(this))
        }
    }, setAddress: function (a) {
        if (a) {
            request = new Ajax.Request(this.addressUrl + a, {
                method: "get",
                onSuccess: this.onAddressLoad,
                onFailure: checkout.ajaxFailure.bind(checkout)
            })
        } else {
            this.fillForm(false)
        }
    }, newAddress: function (a) {
        if (a) {
            this.resetSelectedAddress();
            Element.show("billing-new-address-form")
        } else {
            Element.hide("billing-new-address-form")
        }
    }, resetSelectedAddress: function () {
        var a = $(this.addressSelect);
        if (a) {
            a.value = ""
        }
    }, fillForm: function (transport) {
        var elementValues = {};
        if (transport && transport.responseText) {
            try {
                elementValues = eval("(" + transport.responseText + ")")
            } catch (e) {
                elementValues = {}
            }
        } else {
            this.resetSelectedAddress()
        }
        arrElements = Form.getElements(checkout.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^billing:/, "");
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : "";
                if (fieldName == "country_id" && billingForm) {
                    billingForm.elementChildLoad(arrElements[elemIndex])
                }
            }
        }
    }, saveInAddressBook: function (a) {
        this.update()
    }, update: function () {
        try {
            if (ideDobInst && ideDobInst.isNeedToBind()) {
                ideDobInst.bind()
            }
        } catch (b) {
        }
        var c = Form.serialize(checkout.form);
        checkout.setLoadWaiting("shipping-method");
        checkout.setLoadWaiting("payment-method");
        checkout.setLoadWaiting("review");
        var a = new Ajax.Request(checkout.getBillingUrl(), {
            method: "post",
            onComplete: this.onComplete,
            onSuccess: this.onAfterUpdate,
            onFailure: checkout.ajaxFailure.bind(checkout),
            parameters: c
        })
    }, nextStep: function (transport) {
        var response = null;
        if (transport && transport.responseText) {
            try {
                response = eval("(" + transport.responseText + ")")
            } catch (e) {
                response = {}
            }
        }
        if (response.error) {
            if ((typeof response.message) == "string") {
                alert(response.message)
            } else {
                if (window.billingRegionUpdater) {
                    billingRegionUpdater.update()
                }
                alert(response.message.join("\n"))
            }
            return false
        }
        checkout.setStepResponse(response)
    }, setRegister: function (a) {
        if (a) {
            $(this.registerCustomer).show();
            $(this.customerPassword).focus()
        } else {
            $(this.registerCustomer).hide()
        }
    }, checkEmail: function () {
        var b = $("billing:email");
        Validation.reset(b);
        if (Validation.validate(b, {useTitle: true})) {
            var c = {email: b.getValue()};
            var a = new Ajax.Request(this.checkEmailUrl, {
                method: "post",
                onSuccess: this._checkEmailResponse,
                parameters: c
            })
        }
    }, _checkEmailResponse: function (transport) {
        var response = null;
        if (transport && transport.responseText) {
            try {
                response = eval("(" + transport.responseText + ")")
            } catch (e) {
                response = {}
            }
        }
        if (response.error) {
            if (response.message) {
                if (response.message == "invalid") {
                    Validation.validate(emailElem, {useTitle: true})
                } else {
                    if (response.message == "exists") {
                        ideCheckoutLoginInst.forceLogin(response.errorMessage)
                    }
                }
            }
        }
    },
};
var IdeShipping = Class.create();
IdeShipping.prototype = {
    initialize: function () {
        this.region = "shipping:region";
        this.regionId = "shipping:region_id";
        this.sameAsBilling = "shipping:same_as_billing";
        this.addressSelect = "shipping-address-select";
        this.useForShipping = "billing:use_for_shipping";
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);
        this._addEvents()
    }, _addEvents: function () {
    }, setAddress: function (a) {
        if (a) {
            request = new Ajax.Request(this.addressUrl + a, {
                method: "get",
                onSuccess: this.onAddressLoad,
                onFailure: checkout.ajaxFailure.bind(checkout)
            })
        } else {
            this.fillForm(false)
        }
    }, newAddress: function (a) {
        if (a) {
            this.resetSelectedAddress();
            Element.show("shipping-new-address-form")
        } else {
            Element.hide("shipping-new-address-form")
        }
    }, resetSelectedAddress: function () {
        var a = $(this.addressSelect);
        if (a) {
            a.value = ""
        }
    }, fillForm: function (transport) {
        var elementValues = {};
        if (transport && transport.responseText) {
            try {
                elementValues = eval("(" + transport.responseText + ")")
            } catch (e) {
                elementValues = {}
            }
        } else {
            this.resetSelectedAddress()
        }
        var arrElements = Form.getElements(checkout.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^shipping:/, "");
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : "";
                if (fieldName == "country_id" && checkout.form) {
                    checkout.form.elementChildLoad(arrElements[elemIndex])
                }
            }
        }
    }, saveInAddressBook: function (a) {
        billing.update()
    }, setSameAsBilling: function (a) {
        $(this.sameAsBilling).checked = a;
        $(this.useForShipping).setValue(a == true ? "1" : "0");
        if (a) {
            this.syncWithBilling();
            Element.hide("checkout-step-shipping")
        } else {
            Element.show("checkout-step-shipping")
        }
        if (arguments.length == 1) {
            billing.update()
        }
    }, syncWithBilling: function () {
        $(this.addressSelect) && this.newAddress(!$(billing.addressSelect).value);
        if (!$(billing.addressSelect) || !$(billing.addressSelect).value) {
            var c = Form.getElements(checkout.form);
            for (var a in c) {
                if (c[a].id) {
                    var b = $(c[a].id.replace(/^shipping:/, "billing:"));
                    if (b) {
                        c[a].value = b.value
                    }
                }
            }
            shippingRegionUpdater.update();
            $(this.regionId).value = $(billing.regionId).value;
            $(this.region).value = $(billing.region).value
        } else {
            $(this.addressSelect).value = $(billing.addressSelect).value
        }
    }, setRegionValue: function () {
        $(this.region).value = $(billing.region).value
    }, bindMobile: function (a) {
        if (a.checked) {
            ideMaskInst.mobile9("#checkout-step-shipping .fields.mobile .input-box input")
        } else {
            ideMaskInst.mobile("#checkout-step-shipping .fields.mobile .input-box input")
        }
    }, bindTelephone: function (a) {
        if (a.checked) {
            ideMaskInst.telephone9("#checkout-step-shipping .fields.telephone .input-box input")
        } else {
            ideMaskInst.telephone("#checkout-step-shipping .fields.telephone .input-box input")
        }
    }
};
var IdeShippingMethod = Class.create();
IdeShippingMethod.prototype = {
    beforeValidateFunc: $H({}),
    afterValidateFunc: $H({}),
    isSelectedUniqueShippingMethod: false,
    initialize: function (a, b) {
        this.form = a;
        this.updateShippingMethodUrl = b.updateShippingMethodUrl;
        this.validator = new Validation(this.form);
        this.onAfterUpdate = billing.nextStep.bindAsEventListener(this);
        this.onFailure = checkout.ajaxFailure.bind(checkout);
        this._addEvents()
    },
    _addEvents: function () {
    },
    addBeforeValidateFunction: function (b, a) {
        this.beforeValidateFunc.set(b, a)
    },
    beforeValidate: function () {
        var b = true;
        var a = false;
        (this.beforeValidateFunc).each(function (c) {
            a = true;
            if ((c.value)() == false) {
                b = false
            }
        }.bind(this));
        if (!a) {
            b = false
        }
        return b
    },
    validate: function () {
        var a = this.beforeValidate();
        if (a) {
            return true
        }
        var b = document.getElementsByName("shipping_method");
        if (b.length == 0) {
            alert(Translator.translate("Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.").stripTags());
            return false
        }
        if (!this.validator.validate()) {
            return false
        }
        for (var c = 0; c < b.length; c++) {
            if (b[c].checked) {
                return true
            }
        }
        a = this.afterValidate();
        if (a) {
            return true
        }
        alert(Translator.translate("Please specify shipping method.").stripTags());
        return false
    },
    addAfterValidateFunction: function (b, a) {
        this.afterValidateFunc.set(b, a)
    },
    afterValidate: function () {
        var b = true;
        var a = false;
        (this.afterValidateFunc).each(function (c) {
            a = true;
            if ((c.value)() == false) {
                b = false
            }
        }.bind(this));
        if (!a) {
            b = false
        }
        return b
    },
    update: function () {
        var b = Form.serialize(this.form);
        checkout.setLoadWaiting("payment-method");
        checkout.setLoadWaiting("review");
        var a = new Ajax.Request(this.updateShippingMethodUrl, {
            method: "post",
            onSuccess: this.onAfterUpdate,
            onFailure: this.onFailure,
            parameters: b
        })
    }
};
var IdePayment = Class.create();
IdePayment.prototype = {
    beforeInitFunc: $H({}),
    afterInitFunc: $H({}),
    beforeValidateFunc: $H({}),
    afterValidateFunc: $H({}),
    initialize: function (a, b) {
        this.form = a;
        this.updatePaymentUrl = b.updatePaymentUrl;
        this.updatePaymentAjaxUrl = b.updatePaymentAjaxUrl;
        this.updatePaymentMethodUrl = b.updatePaymentMethodUrl;
        this.onAfterUpdate = billing.nextStep.bindAsEventListener(this);
        this.onFailure = checkout.ajaxFailure.bind(checkout);
        this._addEvents()
    },
    _addEvents: function () {
    },
    addBeforeInitFunction: function (b, a) {
        this.beforeInitFunc.set(b, a)
    },
    beforeInit: function () {
        (this.beforeInitFunc).each(function (a) {
            (a.value)()
        })
    },
    init: function () {
        this.beforeInit();
        var b = Form.getElements(checkout.form);
        var c = null;
        for (var a = 0; a < b.length; a++) {
            if (b[a].name == "payment[method]") {
                if (b[a].checked) {
                    c = b[a].value
                }
            }
            b[a].setAttribute("autocomplete", "off")
        }
        if (c) {
            this.switchMethod(c)
        }
        this.afterInit()
    },
    addAfterInitFunction: function (b, a) {
        this.afterInitFunc.set(b, a)
    },
    afterInit: function () {
        (this.afterInitFunc).each(function (a) {
            (a.value)()
        })
    },
    switchMethod: function (a) {
        if (this.currentMethod && $("payment_form_" + this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
            $("payment_form_" + this.currentMethod).fire("payment-method:switched-off", {method_code: this.currentMethod})
        }
        if ($("payment_form_" + a)) {
            this.changeVisible(a, false);
            $("payment_form_" + a).fire("payment-method:switched", {method_code: a})
        } else {
            document.body.fire("payment-method:switched", {method_code: a})
        }
        if (a) {
            this.lastUsedMethod = a
        }
        this.currentMethod = a
    },
    changeVisible: function (c, b) {
        var a = "payment_form_" + c;
        [a + "_before", a, a + "_after"].each(function (e) {
            var d = $(e);
            if (d) {
                d.style.display = (b) ? "none" : "";
                d.select("input", "select", "textarea", "button").each(function (f) {
                    f.disabled = b
                })
            }
        })
    },
    addBeforeValidateFunction: function (b, a) {
        this.beforeValidateFunc.set(b, a)
    },
    beforeValidate: function () {
        var a = true;
        (this.beforeValidateFunc).each(function (b) {
            if ((b.value)() == false) {
                a = false
            }
        }.bind(this));
        return a
    },
    validate: function () {
        var a = this.beforeValidate();
        if (!a) {
            return false
        }
        if ($$('[name="payment[method]"]:checked').length == 0) {
            alert(Translator.translate("Please specify payment method.").stripTags());
            return false
        }
        var b = document.getElementsByName("payment[method]");
        if (b.length == 0) {
            alert(Translator.translate("Your order cannot be completed at this time as there is no payment methods available for it.").stripTags());
            return false
        }
        a = this.afterValidate();
        if (!a) {
            return false
        }
        return true
    },
    addAfterValidateFunction: function (b, a) {
        this.afterValidateFunc.set(b, a)
    },
    afterValidate: function () {
        var a = true;
        (this.afterValidateFunc).each(function (b) {
            if ((b.value)() == false) {
                a = false
            }
        }.bind(this));
        return a
    },
    update: function () {
        var b = Form.serialize(this.form);
        checkout.setLoadWaiting("review");
        var a = new Ajax.Request(this.updatePaymentAjaxUrl, {
            method: "post",
            onSuccess: this.onAfterUpdate,
            onFailure: this.onFailure,
            parameters: b
        })
    },
    updatePaymentMethod: function (b) {
        var c = {payment_method: b.getValue()};
        checkout.setLoadWaiting("review");
        var a = new Ajax.Request(this.updatePaymentMethodUrl, {
            method: "post",
            onSuccess: this.onAfterUpdate,
            onFailure: this.onFailure,
            parameters: c
        })
    },
    initWhatIsCvvListeners: function () {
        $$(".cvv-what-is-this").each(function (a) {
            a.observe("click", payment.toggleCvv)
        })
    },
    toggleCvv: function (a) {
        if ($("payment-tool-tip")) {
            $("payment-tool-tip").setStyle({top: (Event.pointerY(a)) + "px", left: (Event.pointerX(a) + 30) + "px"});
            $("payment-tool-tip").toggle()
        }
        Event.stop(a)
    }
};
var IdeMask = Class.create();
IdeMask.prototype = {
    initialize: function () {
    }, canApplayMask: function () {
        if (Prototype.Browser.ie9 || Prototype.Browser.ie10) {
            if (parseFloat(Prototype.Version.substring(0, 3)) < 1.7) {
                return false
            }
        }
        return true
    }, telephone: function (a, c) {
        if (this.canApplayMask()) {
            var d = new MaskedInput(a);
            d.unmask();
            var b = c.getValue().replace(/\D/g, "");
            if (b && b.length > 10) {
                d.mask("(99) 99999-999?9")
            } else {
                d.mask("(99) 9999-9999?9")
            }
        }
    }, cpf: function (b) {
        if (this.canApplayMask()) {
            var a = new MaskedInput(b);
            a.unmask();
            a.mask("999.999.999-99")
        }
    }, cnpj: function (a) {
        if (this.canApplayMask()) {
            var b = new MaskedInput(a);
            b.unmask();
            b.mask("99.999.999/9999-99")
        }
    }, day: function (a) {
        if (this.canApplayMask()) {
            var b = new MaskedInput(a);
            b.unmask();
            b.mask("99")
        }
    }, month: function (b) {
        if (this.canApplayMask()) {
            var a = new MaskedInput(b);
            a.unmask();
            a.mask("99")
        }
    }, fullYear: function (b) {
        if (this.canApplayMask()) {
            var a = new MaskedInput(b);
            a.unmask();
            a.mask("9999")
        }
    }
};
var ideMaskInst = new IdeMask();
var IdeDob = Class.create();
IdeDob.prototype = {
    initialize: function (a, e, c, b, d) {
        this.day = $(a);
        this.month = $(e);
        this.year = $(c);
        this.full = $(b);
        this.format = d
    }, bind: function () {
        this.full.value = this.format.replace(/%[mb]/i, this.month.value).replace(/%[de]/i, this.day.value).replace(/%y/i, this.year.value)
    }, isNeedToBind: function () {
        var a = false;
        $$('input[type="radio"][name="billing[tipo_pessoa]"]').each(function (b) {
            if (b.checked && b.value == "F") {
                a = true
            }
        });
        return a
    }
};