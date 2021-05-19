"use strict";
// $(function() {
//     // Side Bar Toggle
//     $('.hide-sidebar').click(function() {
// 	  $('#sidebar').hide('fast', function() {
// 	  	$('#content').removeClass('span9');
// 	  	$('#content').addClass('span12');
// 	  	$('.hide-sidebar').hide();
// 	  	$('.show-sidebar').show();
// 	  });
// 	});

// 	$('.show-sidebar').click(function() {
// 		$('#content').removeClass('span12');
// 	   	$('#content').addClass('span9');
// 	   	$('.show-sidebar').hide();
// 	   	$('.hide-sidebar').show();
// 	  	$('#sidebar').show('fast');
// 	});
// });
let divsession = document.querySelector('#divsession');
let fermerdivsession = document.querySelector('#fermerdivsession');

divsession.addEventListener('click', (e) => { // j'ajoute au bouton un event pour ouvrir le pop up
    console.log(e.target.id);
    if (e.target.id != fermerdivsession.id) {
        if (!e.target.parentNode.classList.contains('open') && e.target.parentNode.id != fermerdivsession.id) {
            // console.log('cliqué: ');
            // console.log(e.target.id);
            e.target.parentNode.classList.add('open');
            console.log('add open');
        } else {
            console.log('nada');
        }
    }
});

fermerdivsession.addEventListener('click', (e) => { // j'ajoute au bouton un event pour fermer le pop up
    console.log('cliqué: ' + e.target.id);
    console.log('jenleve: open');

    console.log(fermerdivsession.classList);
    divsession.classList.remove('open');

    if (divsession.classList.contains('open')) {
        console.log('ya encore open');
    } else {
        console.log('ya plus open');
    }
});

window.addEventListener('resize', function(event) {
    // refresh_affichage(0); // si le user resize la page ! pffff' on relance le calcul ;(
});

if (document.querySelector('#sourcesql') && document.querySelector('#refreshsourcesql') && document.querySelector('#sqlstring')) {
    let refreshsourcesql = document.querySelector('#refreshsourcesql');

    refreshsourcesql.addEventListener('click', (e) => {
        let text = ""
        let skeepid = document.querySelector('#skeepid').checked;
        let skeepname = document.querySelector('#skeepname') // && skeepid && document.querySelector('#skeepname').value != "" ? document.querySelector('#skeepname').value : "";
        let intitules = document.querySelector('#sourcesql thead tr').childNodes;
        let sourcesql = document.querySelector('#sourcesql tbody');
        let targettextearea = document.querySelector('#sqlstring');
        let liste = sourcesql.childNodes

        console.log(intitules);
        console.log("skeeped name:" + skeepname.value);


        liste.forEach(element => {
            let newline = ""
            let i = 0
            element.childNodes.forEach(element2 => {
                if (element2.childNodes[0] && element2.childNodes[0].nodeType == Node.TEXT_NODE) {
                    if (skeepid && intitules[i].textContent === '[' + skeepname.value + ']') {
                        newline += ((newline === "") ? ((text === "") ? "(" : ",\r\n(") : ",") + 'NULL'
                    } else {
                        let car = (intitules[i].getAttribute('d')) ? "" : "'"
                        newline += ((newline === "") ? ((text === "") ? "(" : ",\r\n(") : ",") + car + element2.childNodes[0].nodeValue + car
                    }
                }
                i++
            });
            newline += newline != "" ? ")" : ""
            text += newline
        });
        text = "INSERT INTO '" + document.querySelector('#sourcesql').getAttribute('db') + "' VALUES \r\n" + text
        targettextearea.textContent = ''
        targettextearea.textContent = (text ? text + ";" : "")

    })


}