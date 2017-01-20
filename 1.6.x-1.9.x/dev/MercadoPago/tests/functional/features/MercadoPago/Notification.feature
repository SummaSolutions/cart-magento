@MercadoPago
Feature: Notification received in MercadoPago Standard Checkout

Background:
  Given User "test_user_58666377@testuser.com" "magento" exists
  And Setting Config "payment/mercadopago_standard/sandbox_mode" is "0"
  And I wait for "5" seconds
  And I am logged in as "test_user_58666377@testuser.com" "magento"
  And I am on page "swiss-movement-sports-watch.html"
  And I press ".add-to-cart-buttons .btn-cart" element
  And I press ".btn-proceed-checkout" element
  And I fill the billing address
  And I press "#billing-buttons-container .button" element
  And I select shipping method "s_method_flatrate_flatrate"
  And I wait for "3" seconds
  And I press "#shipping-method-buttons-container .button" element
  And I wait for "3" seconds
  And I select radio "p_method_mercadopago_standard"
  And I press "#payment-buttons-container .button" element
  And I wait for "3" seconds
  And I press "#review-buttons-container .button" element
  And I wait for "3" seconds
  And I switch to the iframe "checkout_mercadopago"


  @StandardNotification
  Scenario: Send the notification and check order state
    And I fill the iframe fields
    And I press "#next" input element
    And I switch to the site
    And I should be on "/mercadopago/success"
    And I send the Notification
    And I wait for "3" seconds

  @TwoCardPayment
  Scenario: Pay the order with two cards, send the notification and check order state
    And I press ".init-step-link" input element
    And I wait for "5" seconds
    And I fill the two card iframe fields
    And I press "#next-split" input element
    And I wait for "3" seconds
    And I switch to the iframe "checkout_mercadopago"
    And I fill the iframe fields
    And I wait for "3" seconds
    And I press "#next" input element
    And I wait for "3" seconds
    And I switch to the site
    And I switch to the iframe "checkout_mercadopago"
    And I fill the iframe fields
    And I wait for "3" seconds
    And I press "#next" input element
    And I switch to the site
    And I should be on "/mercadopago/success"
    And I send the Notification
    And I wait for "10" seconds

  @NotificationThenRefund
  Scenario: Made a refund, the order must be on the correct state
    And I fill the iframe fields
    And I press "#next" input element
    And I switch to the site
    And I should be on "/mercadopago/success"
    And I send the Notification
    And I wait for "3" seconds
    Given Setting Config "payment_mercadopago_refund_available" is "1"
    Given Setting Config "payment_mercadopago_order_status_refunded" is "holded"
    And I am admin logged in as "admin" "MercadoPago2015"
    Then I go to the Order
    And I wait for "2" seconds
    Then I press ".order-totals-bottom .save" input element
    Then Order must have credit memos
    And Order must be in the correct state