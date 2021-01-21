<?php

namespace Flex301\Cropperjs;

use yii\bootstrap\Html;
use yii\widgets\InputWidget;

class FileInput extends InputWidget
{
    public function init()
    {
        parent::init();

        $view = $this->getView();
        CropperAsset::register($view);
    }

    public function run()
    {
        $inputId = Html::getInputId($this->model, $this->attribute);
        $imageSrc = $this->model->{$this->attribute};
        $pInputName = Html::getInputName($this->model, 'p');
        $imageId = $inputId . "-image";
        $buttonId = $inputId . "-button";
        $pInputid = $inputId . "-p";

        echo Html::button('Выбрать изображение', ['class' => 'btn btn-primary btn-md mb-2 d-block', 'id' => $buttonId]);
        echo Html::img($imageSrc, ['id' => $imageId, 'style' => 'display: block; max-width: 100%;']);
        echo Html::activeInput('file', $this->model, $this->attribute, ['id' => $inputId, 'class' => 'd-none', 'accept' => 'image/*']);
        echo Html::hiddenInput($pInputName, "{}", ['id' => $pInputid]);

        $view = $this->getView();

        $view->registerJs(
            "
            (function() {
                let cropper = null;
                const inputElem = $('#" . $inputId . "');
                const pElem = $('#" . $pInputid . "');

                $('#" . $buttonId . "').click(function() {
                    inputElem.click();
                });

                $('#" . $inputId . "').change(function() {
                    const image = $('#" . $imageId . "');
                    const imageElem = image[0];
                    const imageUrl = URL.createObjectURL(this.files[0]);
                    image.attr('src', imageUrl);
                    if (!cropper) {
                        cropper = new Cropper(image[0], {
                            aspectRatio: 2,
                            viewMode: 1,
                            zoomable: false
                        });

                        imageElem.addEventListener(\"ready\", function() {
                            this.cropper.move(0, 0);
                        });    

                        imageElem.addEventListener(\"crop\", function(event) {
                            pElem.val(JSON.stringify(event.detail));
                        });    
                    } else {
                        cropper.replace(imageUrl);
                    }
                });
            })();
            "
        );
    }
}