<?php

/*
 * Plugin Name: os Event form
 * Plugin URI: bibliotecadeterminus.xyz
 * Description: Sincronizacion de Eventos
 * Version: 1.0.0
 * Author: Oscar Sanchez
 * Author URI: bibliotecadeterminus.xyz
 * Requires at least: 4.0
 * Tested up to: 4.3
 *
 * Text Domain: wpos-additional
 * Domain Path: /languages/
 */
include 'Model/PodsEventSubscriberRepository.php';
include 'Model/EmailEventSubscriberSender.php';


add_action('wp_ajax_nopriv_event__register', 'event__register');
add_action('wp_ajax_event__register', 'event__register');

function event__register() {

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    $people = filter_input(INPUT_POST, 'people', FILTER_SANITIZE_STRING);
    $event = filter_input(INPUT_POST, 'event', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);


    if(!isset($email)) {
        wp_send_json(['message' => __('Error invalid email', 'wpduf')], 200);
    }

    if(!is_email($email)) {
        wp_send_json(['message' => __('La reserva tiene que tener un email') , 'status' =>'error'], 200);
    }

    if(!isset($token)) {
        wp_send_json(['message' => __('Error en el captcha', 'wpduf'),'status' =>'error'], 500);
    }

    if(!isset($name)) {
        wp_send_json(['message' => __('La reserva tiene que tener un nombre', 'wpduf'),'status' =>'error'], 200);
    }

    if(!is_numeric($people)) {
        wp_send_json(['message' => __('Error en el numero de invitados', 'wpduf'),'status' =>'error'], 200);
    }

    if($people > 15) {
        wp_send_json(['message' => __('Máximo 15 invitados', 'wpduf'),'status' =>'error'], 200);
    }

    if($people < 1) {
        wp_send_json(['message' => __('Minimo 1 invitado', 'wpduf'),'status' =>'error'], 200);
    }


    $secretKey = getPodOptionsNewsletter('options','recaptcha_secret_key');

    $recaptchaValidate = new RecaptchaValidate($secretKey,$token);
    if(!$recaptchaValidate->validateCaptcha()){
        wp_send_json(['message' => __('Error en el captcha', 'wpduf'),'status' =>'error'], 200);
    }

    $eventSubscriberRepository = new PodsEventSubscriberRepository();

    if($eventSubscriberRepository->existSubscriberInEvent($event,$email)) {
        wp_send_json(['message' => __('Ya estas subscrito en el evento', 'wpduf'),'status' =>'error'], 200);
    }
    $eventSubscriberRepository->addSubscriber($email,$phone,$people,$event, $name);

    $emailAdmin = getPodOptions('options','email_receiver');
    $phoneAdmin = getPodOptions('options','phone');

    $emailSender = new EmailEventSubscriberSender($emailAdmin);
    $emailSender->sendToAdmin($name,$email, $people,$phone,$event);
    $emailSender->sendToSubscriber($name,$email, $people,$phone,$event,$emailAdmin,$phoneAdmin);


    wp_send_json(['message' => __('Te has apuntado al evento gracias! Te llamaremos para confirmar los datos', 'wpduf'),'status' =>'ok'], 200);



}