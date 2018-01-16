/**
 *  o-todo
 *  https://github.com/olback/o-todo
 *
 *  This also needs a re-write :3
 *
 */

let api_url = 'assets/test/sample-list.json';
const API_KEY = '';
//let api_url = 'api/api.php';

const log = console.log;
const current_year = new Date().getFullYear();

window.onload = () => {

    const year_elements = document.getElementsByClassName('current-year');
    const side_menu = document.getElementById('side-menu');
    const list = document.getElementById('list');
    const body = document.getElementsByTagName('body');

    // Fill in the current year.
    for(let i = 0; i < year_elements.length; i++) {

        year_elements[i].innerHTML = current_year;

    }

    // Set date to today.
    document.getElementById('new-note-create-date').valueAsDate = new Date();

    // Open menu.
    document.getElementById('menu-button').onclick = () => {

        side_menu.style.width = '300px';

    }

    // Close meny when clicking outside.
    document.getElementById('main').onclick = () => {

        side_menu.style.width = 0;

    }

    // Let the user use the scroll wheel to scroll sideways.
    list.onwheel = (e) => {

        side_menu.style.width = 0;

        if(e.deltaY > 0) {

            list.scrollBy(e.deltaY, 0);

        } else {

            list.scrollBy(e.deltaY, 0);

        }

    }

    // When scrolling, close the nav.
    list.onscroll = () => {

        side_menu.style.width = 0;

    }

    // Close sidenav when 'swipe-closing'.
    let s_txs;
    side_menu.ontouchstart = (e) => {

        s_txs = e.touches[0].clientX;

    }

    side_menu.ontouchmove = (e) => {

        if(s_txs - e.touches[0].clientX > 50) {
            side_menu.style.width = 0;
        }

    }

    // Open sidenav on swipe from the left edge.
    let b_txs;
    body[0].ontouchstart = (e) => {

        b_txs = e.touches[0].clientX;

    }

    body[0].ontouchmove = (e) => {

        if(b_txs <= 50 && (e.touches[0].clientX - b_txs) > 50) {

            side_menu.style.width = '300px';

        }

    }

    // Close modal when clicking the X
    const modal_close_buttons = document.getElementsByClassName('close-modal');
    for(let i = 0; i < modal_close_buttons.length; i++) {

        modal_close_buttons[i].onclick = (e) => {

            let modal = modal_close_buttons[i].parentElement.parentElement;
            modal.style.display = 'none';

        }

    }

    // Add note button action
    const add_note_buttons = document.getElementsByClassName('add-note-button');
    for(let i = 0; i < add_note_buttons.length; i++) {
        add_note_buttons[i].onclick = () => {
            openModal('add-note');
        }
    }

    document.getElementById('profile-button').onclick = () => {
        openModal('profile');
    }

    document.getElementById('admin-button').onclick = () => {
        openModal('admin');
    }

    function openModal(m) {

        side_menu.style.width = 0;
        document.getElementById(m).style.display = 'block';
    
    }

    const articles = document.getElementsByTagName('article');

    for(let i = 0; i < articles.length; i++) {

        articles[i].onclick = (e) => {
            document.getElementById('edit-note').style.display = 'block';
            document.getElementById('edit-note-title').value = articles[i].getAttribute('title');
            document.getElementById('edit-note-note').innerHTML = articles[i].getAttribute('note');
            document.getElementById('edit-note-due-date').value = articles[i].getAttribute('due');
            document.getElementById('edit-note-importance').value = articles[i].getAttribute('importance');
            document.getElementById('edit-note-id').value = articles[i].getAttribute('note-id');
            // TODO: Send updated note to database.
        }

        let a_pos_y;
        articles[i].ontouchstart = (e) => {
            a_pos_y = e.touches[0].clientY;
        }

        articles[i].ontouchmove = (e) => {
            let diff = a_pos_y - e.touches[0].clientY;

            if(diff > 150) {
                swipeUp(articles[i]);
                // TODO: Remove note from database.
            }
        }

    }

    // Click to copy API Key
    const api_key = document.getElementById('api-key');
    api_key.onclick = () => {
        api_key.select();
        document.execCommand('copy');
    }


    document.getElementById('refresh').onclick = () => {
        window.location.reload();
    }

}

// Show 'Add Note hint' when there are no articles
function showHint() {
    let cn = list.childNodes;
    for(let i = 0; i < cn.length; i++) {
        if(cn[i].nodeName == 'ARTICLE') {
            if(cn[i].style.display != 'none') {
                document.getElementById('add-note-hint').style.display = 'none';
                break;
            }
        } else {
            document.getElementById('add-note-hint').style.display = 'block';
        }
    }
}

// Handle the swipeUp action.
function swipeUp(article) {

    if(article) {
        article.style.opacity = '0';
        setTimeout(() => {
            article.style.display = 'none';
            showHint();
        }, 300);
    }

}

function json(response) {
    //log(response.status + ' ' + response.statusText);
    return response.json();
}

function fetchNotes(key) {

    fetch(api_url+'?api_key='+API_KEY+'&action=list', {
        method: 'get',
        headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        //body: "?api_key="+key+"&action=fetchNotes",
        credentials: 'include'
    })
    .then(json)
    .then(function (data) {
        //log('Request succeeded with JSON response', data);

        for(let i = 0; i < data.notes.length; i++) {

            let article = document.createElement('article');
            let h3 = document.createElement('h3');
            let p = document.createElement('p');
            let span = document.createElement('span');
            h3.innerHTML = data.notes[i].title;
            p.innerHTML = data.notes[i].body;
            span.innerHTML = data.notes[i].due;
            article.appendChild(h3);
            article.appendChild(p);
            article.appendChild(span);

            article.setAttribute("title", data.notes[i].title);
            article.setAttribute("note", data.notes[i].body);
            article.setAttribute("due", data.notes[i].due);
            article.setAttribute("note-id", data.notes[i].id);
            article.setAttribute("created", data.notes[i].created);
            article.setAttribute("importance", data.notes[i].importance);
    
            list.appendChild(article);
            showHint();
        }
    })
    .catch(function (error) {
        console.log('Request failed', error);
    });

}

// Register service worker
if ('serviceWorker' in navigator) {

    navigator.serviceWorker.register('service-worker.js').then(() => {
        log('Service-worker registerd');
    })

} else {

    console.error('Service-workers not supported.');

}

fetchNotes();
