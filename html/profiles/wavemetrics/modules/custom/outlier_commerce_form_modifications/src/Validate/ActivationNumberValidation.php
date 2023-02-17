<?php
namespace Drupal\outlier_commerce_form_modifications\Validate;

use Drupal\Core\Field\FieldException;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form API callback. Validate element value.
 */
class ActivationNumberValidation
{
    /**
     * Validates the Igor Activation Number element.
     *
     * We do this on the form level and not the element because we match Version number (A separate field)
     *
     * @param array $element The form element to process.
     * @param FormStateInterface $form_state The form state.
     * @param array $form The complete form structure.
     */
    public static function validate(array &$form, FormStateInterface $form_state)
    {
        $key = $form_state->getValue('key'); //as in activation KEY
        $version = $form_state->getValue('version');
        $error = false;
        $errorMessage = "";

        // Check activation key
        $key = trim($key);        // Strip leading and trailing white space.

        //see if this validates as a Igor 7+ activation number.
        if($version<7) {
            list($part1, $part2, $part3, $part4, $part5, $part6) = explode("-", $key, 6);

            $activationKeyError = "Expected an activation key of the form ABCD-EFGH-IJLK-MNOP-QR";

            if (strlen($part6) != 0) {
                $errorMessage = "$activationKeyError (part6 is wrong)";
                $error = true;
            }
            if (strlen($part5) != 2) {
                $errorMessage = "$activationKeyError (part5 is wrong)";
                $error = true;
            }
            if (strlen($part4) != 4) {
                $errorMessage = "$activationKeyError (part4 is wrong)";
                $error = true;
            }
            if (strlen($part3) != 4) {
                $errorMessage = "$activationKeyError (part3 is wrong)";
                $error = true;
            }
            if (strlen($part2) != 4) {
                $errorMessage = "$activationKeyError (part2 is wrong)";
                $error = true;
            }
            if (strlen($part1) != 4) {
                $errorMessage = "$activationKeyError (part1 is wrong)";
                $error = true;
            }
        }

        //if not, see if it validates as a Igor <7 activation number.
        elseif($version==7) {
            list($part1, $part2, $part3, $part4, $part5, $part6, $part7, $part8) = explode("-", $key, 8);
            $activationKeyError = "Expected an activation key of the form ABCD-EFGH-IJLK-MNOP-QRST-UVWX-Y";

            if (strlen($part8) != 0) {
                $errorMessage = "$activationKeyError (part8 is wrong)";
                $error = true;
            }

            if (strlen($part7) != 1) {
                $errorMessage = "$activationKeyError (part7 is wrong)";
                $error = true;
            }

            if (strlen($part6) != 4) {
                $errorMessage = "$activationKeyError (part6 is wrong)";
                $error = true;
            }

            if (strlen($part5) != 4) {
                $errorMessage = "$activationKeyError (part5 is wrong)";
                $error = true;
            }

            if (strlen($part4) != 4) {
                $errorMessage = "$activationKeyError (part4 is wrong)";
                $error = true;
            }

            if (strlen($part3) != 4) {
                $errorMessage = "$activationKeyError (part3 is wrong)";
                $error = true;
            }

            if (strlen($part2) != 4) {
                $errorMessage = "$activationKeyError (part2 is wrong)";
                $error = true;
            }

            if (strlen($part1) != 4) {
                $errorMessage = "$activationKeyError (part1 is wrong)";
                $error = true;
            }

        }
        elseif($version>=8) {
            list($part1, $part2, $part3, $part4, $part5, $part6, $part7, $part8) = explode("-", $key, 8);
            $activationKeyError = "Expected an activation key of the form ABCD-EFGH-IJLK-MNOP-QRST-UVWX-YZA";

            if (strlen($part8) != 0) {
                $errorMessage = "$activationKeyError (part8 is wrong)";
                $error = true;
            }

            if (strlen($part7) != 3) {
                $errorMessage = "$activationKeyError (part7 is wrong)";
                $error = true;
            }

            if (strlen($part6) != 4) {
                $errorMessage = "$activationKeyError (part6 is wrong)";
                $error = true;
            }

            if (strlen($part5) != 4) {
                $errorMessage = "$activationKeyError (part5 is wrong)";
                $error = true;
            }

            if (strlen($part4) != 4) {
                $errorMessage = "$activationKeyError (part4 is wrong)";
                $error = true;
            }

            if (strlen($part3) != 4) {
                $errorMessage = "$activationKeyError (part3 is wrong)";
                $error = true;
            }

            if (strlen($part2) != 4) {
                $errorMessage = "$activationKeyError (part2 is wrong)";
                $error = true;
            }

            if (strlen($part1) != 4) {
                $errorMessage = "$activationKeyError (part1 is wrong)";
                $error = true;
            }

        }
        else{
            $error = true;
        }


        if ($error) {

            $tArgs = array(
                '%error_message' => $errorMessage,
            );
            $form_state->setErrorByName('register_activation',  t('%error_message', $tArgs));


        }

    }
}
?>