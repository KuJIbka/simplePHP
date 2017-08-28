<?php

namespace Main\Service;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;
use Symfony\Component\Translation\Translator;

class ExpandTranslator extends Translator
{
    public function trans($idOrLocalisationString, array $parameters = [], $domain = null, $locale = null)
    {
        if ($idOrLocalisationString instanceof LocalisationString) {
            $id = $idOrLocalisationString->getKey();
            $parameters = !empty($parameters) ? $parameters : $idOrLocalisationString->getData();
            $domain = $domain ?: $idOrLocalisationString->getDomain();
            $locale = $locale ?: $idOrLocalisationString->getLocale();
            return parent::trans($id, $parameters, $domain, $locale);
        } else {
            return parent::trans($idOrLocalisationString, $parameters, $domain, $locale);
        }
    }

    public function transChoice(
        $idOrLocalisationString,
        $number = null,
        array $parameters = array(),
        $domain = null,
        $locale = null
    ) {
        if ($idOrLocalisationString instanceof LocalisationString) {
            $id = $idOrLocalisationString->getKey();
            $parameters = !empty($parameters) ? $parameters : $idOrLocalisationString->getData();
            $domain = $domain ?: $idOrLocalisationString->getDomain();
            $locale = $locale ?: $idOrLocalisationString->getLocale();
            $number = $number ?: $idOrLocalisationString->getChoiceNumber();
            return parent::transChoice($id, $number, $parameters, $domain, $locale);
        } else {
            return parent::transChoice($idOrLocalisationString, $number, $parameters, $domain, $locale);
        }
    }

    /**
     * @param String|LocalisationString|LocalisationChoiceString $localisationString
     */
    public function transLocalisationString($localisationString)
    {
        if ($localisationString instanceof LocalisationChoiceString) {
            return $this->transChoice($localisationString);
        } else {
            return $this->trans($localisationString);
        }
    }
}
