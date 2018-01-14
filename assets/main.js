/**
 *  o-todo
 *  https://github.com/olback/o-todo
 *
 *  This also needs a re-write :3
 *
 */

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

    // Open menu.
    document.getElementById('menu-button').onclick = () => {

        side_menu.style.width = '300px';

    }

    // Close meny when clicking outside.
    document.getElementById('main').onclick = () => {

        side_menu.style.width = 0;

    }

    // What does this do??
    // list.ondrag = (e) => {

    //     list.scrollBy(e.deltaY, 0);

    // }

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
            // TODO: Edit note.
        }

        let a_pos_y;
        articles[i].ontouchstart = (e) => {
            a_pos_y = e.touches[0].clientY;
        }

        articles[i].ontouchmove = (e) => {
            let diff = a_pos_y - e.touches[0].clientY;

            if(diff > 100) {
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


}

// Handle the swipeUp action.
function swipeUp(article) {

    article.style.opacity = '0';
    setTimeout(() => {
        article.style.display = 'none';
    }, 300);


}

// Register service worker
if ('serviceWorker' in navigator) {

    navigator.serviceWorker.register('service-worker.js').then(() => {
        log('Service-worker registerd');
    })

} else {

    console.error('Service-workers not supported.');

}
