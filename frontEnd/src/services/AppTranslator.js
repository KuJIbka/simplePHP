import Translator  from 'bazinga-translator';

const trans = {
    "а": "a",
    "б": "b",
    "в": "v",
    "г": "g",
    "д": "d",
    "е": "e",
    "ё": "io",
    "ж": "zh",
    "з": "z",
    "и": "i",
    "й": "y",
    "к": "k",
    "л": "l",
    "м": "m",
    "н": "n",
    "о": "o",
    "п": "p",
    "р": "r",
    "с": "s",
    "т": "t",
    "у": "u",
    "ф": "f",
    "х": "h",
    "ц": "ts",
    "ч": "ch",
    "ш": "sh",
    "щ": "sht",
    "ъ": "a",
    "ы": "i",
    "ь": "y",
    "э": "e",
    "ю": "yu",
    "я": "ya",
    "А": "A",
    "Б": "B",
    "В": "V",
    "Г": "G",
    "Д": "D",
    "Е": "E",
    "Ё": "Io",
    "Ж": "Zh",
    "З": "Z",
    "И": "I",
    "Й": "Y",
    "К": "K",
    "Л": "L",
    "М": "M",
    "Н": "N",
    "О": "O",
    "П": "P",
    "Р": "R",
    "С": "S",
    "Т": "T",
    "У": "U",
    "Ф": "F",
    "Х": "H",
    "Ц": "Ts",
    "Ч": "Ch",
    "Ш": "Sh",
    "Щ": "Sht",
    "Ъ": "A",
    "Ы": "I",
    "Ь": "Y",
    "Э": "e",
    "Ю": "Yu",
    "Я": "Ya",
};

class AppTranslator {
    static locale = Translator.locale;

    static trans(string, params, domain, locale) {
        return Translator.trans(string, params, domain, locale);
    }

    static transChoice(string, number, params, domain, locale) {
        return Translator.transChoice(string, number, params, domain, locale);
    }

    static toTranslit(toTranslit) {
        console.log(Translator);
        for (let i = 0; i < toTranslit.length; i++) {
            let ch = toTranslit[i];
            if (trans[ch]) {
                let toCh = trans[ch];
                let reg = new RegExp(ch, "g");
                toTranslit = toTranslit.replace(reg, toCh);
            }
        }
        return toTranslit;
    }
}

export default AppTranslator;