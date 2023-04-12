<?php

/*

Legend of the help text structure:

The help text structure is an array of nested arrays.

The first element of a paragraph array is the title string. The second is an array of line strings, in web form they begin with a unordered list bullet (little circle).

If the line string begins with a '^' character the line is shown without a bullet.  

If it begins with a '=' character, followed by a wildcard and someting in quotes, the line is shown indented, with the text in quotes - bold, and the wild card in front of the quotes. In the web form it looks like kind of sub line items.

Hyperlinks in line strings are surrounded by :; characters in front and at the end of the link.

*/


$item_list = 

[

  [ "Basics",

    [
      "The combinator generates combinations in a random way (see below) based on a specific character set, characters number,  desired number of combinations.",

      "You can define the character set in two ways via the button \"Character set...\" \n",

      "^and the \"Complexity requirements\" check boxes (these are different). ",

      "There are four methods of doing the job - chosen by one of the four radio buttons (described below).",

      "The main action buttons are \"Generate\" and \"Reset all...\". They are self explanatory, the last nullifies all last settings."
    ]
  ],

  [ "Complexity requirements",
    [
    "Only printable ASCII characters are used for combinations, divided by five classes: smalls, capitals, digits, specials, space.",

    "There are optional requirements for every of them. The default is all first four and space character -the last deliberately omitted.",

    "The meaning of requirements is - if such is chosen, AT LEAST one character from this class MUST be present in the combination(s). If not - NONE of this class MUST be present.",

    "This is different from \"needs\", which are defined in \"Character set specifying\" (see bellow [3.]).",

    "There is a text edit control under the name \"Need to exclude characters:\". This is a implicitly a requirement, because excluded characters MUST NOT be present in the combination(s). It is concerned in character set specifying, also. (see bellow [3.])",

    "Mind it that at first the character set shown in \"Current character set\" text box will not be updated. It will be done after generating combinations via \"Generate\" button, or using the character set specifying option. If you need a character set using refining - use the \"Character set...\" button  (see 3.). The main form is only for fast generation with common requirements."
    ]
  ],

  [ "Character set specifying",

    [
     "^(As it was said in 2. that specifying is different from requirements, the last ones are obligatory in generation (see).) \n",

     "There is a text box under the text \"Current character set:\" in which the complete current character set is shown.",
     "The character set is specified precisely in the form when pressing \"Character set...\" button.",
     "Its meaning is that all that characters MAY, OR MAY NOT be present in the combination(s). The strong rules are defined by \"requirements\" (see 2.)",
     "Pressing the \"Character set...\" opens a form for specifying the character set by using \"needs\". They are similar to requirements, but in the soft, necessity dependency (MAY OR MAY NOT be present).",
     "The form header says: \"Specify the character set:\". The needs use the same character classes as the requirements,  except that letter characters are defined as from \"this one\"- to \"that one\". There is an exclude string too.",
     "The generation of the character set is done by pressing the button \"Preview it\". The resultant character set is shown in the text box (read only) under the name \"Preview of your choice:\". (The other buttons will be explained bellow.)",
     "After you see you character set you can repeat the setting cycle to the point it is acceptable for you. Returning to the generation form is done by pressing the button \"Send\".",
     "There is also a button \"Type or paste your character set\". It gives you the opportunity to use a purely custom character set by typing manually or pasting it from clipboard. It has to be up to the requirements mentioned above. Not repeating characters, etc. Also it cannot have non ASCII characters - a new line for instance. In this case there you should not do \"Preview\", because it is supposed that you want the exact character set entered. Also \"Preview\" will order it by class and alphanumerical order. The same case is with \"Shuffle\" (see).",
     "The button \"Shuffle the character set...\" allows reordering the existing character in a random way. This concerns more randomness of combinations and if you take seruous care about the character set substance. The last one matters if you are more familiar with the algorithm (see 6.). The shuffle function uses an algorithm which is cryptographic secure. One should be careful that \"Preview\" will reorder the character set. Do not use it in this case.",
     "When moving to the generation form  (via \"Send\"), your \"needs\" choice is applied to the requirements if it concerns the lack or presence of a whole class of characters. If you have chosen a range of characters in a class, the requirement will be present, and the range will be shown below the requirements controls. After that",
    "^if you change the requirements settings, they will prevail and will add or remove the whole class in question. The changes will not be shown immediately in the \"Current character set\" text box, only after some action taken. But they will be in effect. (see 2.)."

    ]
  ],

  [ "Job methods",

    [
     "^(When you hover the mouse over the radio button text you get some info)",
     "ordinary way",
     "^After the number of characters in a combination and the number of combinations is set, you can generate them by pressing the button \"Generate\". A new form - \"Result\" is opened, with a table in it containing the combinations in numbered style. In that form you can do the following things:",
     "=*  \"Go back...\" - going back to the main form.",
     "=*  \"Do it again...\" - do the generation again with the same settings.",
     "=*  \"download as csv (tab delimited)\" - this gives you the opportunity to save the result table in a file in csv format with a name of your choice, in location of your choice. Take attention - the csv is tab delimited. This is because we can use any ASCII printable character for content - so we don't want to mess the table structure.",
     "map ids from csv",
     "^Some times you want to combine the combinations (passwords) with some other identification. Let's say it is a serial number. In that case, choosing this radio option and pressing \"Generate\", you will be offered to choose csv file containing the ids. Its format is one column table. Then the result will be a three columns table  - row number, mappedID, combination (password). In this case the number of combinations is defined by the number of ids in the file. If you click \"Do it again...\" a new table will be generated with the same ids, but other combinations (passwords); there is no connection between the two in that option. Again, you can save the result in a tab delimited csv file, clicking \"download as csv (tab delimited)\". It is a useful option if you need a comprehensive table of identifications linked with passwords.",
     "produce from ids (csv)",
     "^This is kind of similar to \"map ids from csv\", but with a very important different feature. It links the ids and combinations (passwords) in a hard algorithmic way. There are cases when forgetting or losing the password is catastrophic. For example the password cannot be changed anymore forever. This is the case with BIOS passwords of some laptops. The latest is intended by the manufacturer for security reasons. Some solution for this is to derive the passwords from something well known like serial key or similar. In that program uses a hashed form of id, so the derivation is one way. There are also some hard rules concerning the complexity requirements. These are: smalls, CAPS, digits only. No specials, no space. Excluding characters: hijlnoIJOQ01 . This is made because of readability considerations. (The excluded are easily confused with each others.) Pressing \"Generate\" leads to a similar form as in \"map ids from csv\" case, but with a main difference - every combination (password) is dependent of its corresponding id. Pressing \"Do it again...\" will produce the same result. Everything other is the same - \"download as csv (tab delimited)\", and \"Go back...\". The option should be used with attention and only when you are highly convinced it that it is worth it.",
     "produce from single id (input)",
     "^This is the completely the same case as the former (\"produce from ids (csv)\"), does not need a csv file for input, just a copy and paste or writing in hand in a visual edit control. Some times we want to process only one id, and creating a csv file for this is just cumbersome. When you use it and press \"Generate\", it will show you a little dialog box with text \"put your id\" and text edit control. Pressing \"OK\" will open the known form but with one row table respectively. Pressing \"Do it again...\" will not change anything, if you want another one, just \"Go back...\" and  \"Generate\" again.",
     "^_",
     "^(Important note: the \"Reset all...\" button is highly recommended if you want to start everything completely over without much clicking confusion.)"

    ]

  ],

  [ "The generation is done bt pressing the button \"Generate\". This is described in more details in every job method case (see 4.)",

    []

  ],

  [ "Algorithm",

    [
     "Not so important, but is necessary to see the whole picture. For the curious ones. The algorithm for random generation in this program is different from the common used in most of the other programs. Generalized in short - they do something like this. There is a charset in a form of array and it contains the characters that are necessary in the case (for example letters and digits, or something else). A random digit is chosen from the array indices range and it is added to the result string until the required length is achieved. If the resultant combination does not satisfy the initially put conditions (for example - about letters an digits - there is no a digit), the combination is rejected and the cycle goes on  again until everything is satisfied. It is done until the number of required combinations available.",
     "^Here in this program the concept is different. The combination of symbols (password) is thought as one big number itself. In fact it is a number. Any combination of symbols can be viewed as a number in a corresponding number system. Still there is one condition that the number system has to be a bijective one. It guarantees exhaustiveness of the combination variants. This means that every combination is unique. It is fulfilled by not including a symbol that signifies zero, or \"nothing\". Of course there could be a symbol '0', but it cannot mean the number zero. For instance, because of this reason, our common 10 based number system is not bijective. It allows existing of leading zeroes, which don't change the value of the number. If we take these - 123, 0123, and 00123, they count to the same number value but from combinations point of view they are different combinations - 3, 4, and 5 characters long, respectively. The type of number systems used in this program are positional ones, they are alike the popular no bijective systems which are used worldwide - binary, octal, hexadecimal, etc., but with the condition mentioned above. In this implementation there is a \"charset\" which represents the set of \"digits\" used in the corresponding number system. It currently consists of all printable ASCII characters (including space) which are 95 in number. The character order of the set is very important for the numbers representation. The first one means 1 (one), the second - 2 (two), etc. For example if we exchange some digits in our common 10 based number system set, the numbers built on it will look quite different from the normal way. Of course if we are looking for randomness it does not matter too much. Even the opposite - shuffling the character set offers (see 3.) more options in this regard.",
     "The main question when it comes to programs of this type is: how random is the calculation algorithm here. In other words said - how cryptographically secure it is.  The algorithm in this program is cryptographically secure. It can be confirmed by the following fact - for generation of random numbers it uses the PHP function  random_bytes (the software is written mainly on PHP). The php.net site states: \"random_bytes — Generates cryptographically secure pseudo-random bytes\" (for more information see: :;https://www.php.net/manual/en/function.random-bytes.php:;). The character set shuffling feature is also cryptographically secure  - it uses the PHP function random_int, php.net: \"random_int — Generates cryptographically secure pseudo-random integers\" (see :;https://www.php.net/manual/en/function.random-int.php:;)"

    ]

  ]


]









?>
