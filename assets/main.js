/*
    o-todo
    https://github.com/olback/o-todo

*/

const log = console.log;
const current_year = new Date().getFullYear();


window.onload = () => {

    const year_elements = document.getElementsByClassName('current-year');
    const side_menu = document.getElementById('side-menu');
    const list = document.getElementById('list');
    const body = document.getElementsByTagName('body');

    for(let i = 0; i < year_elements.length; i++) {

        year_elements[i].innerHTML = current_year;

    }

    document.getElementById('menu-button').onclick = () => {

        side_menu.style.width = '300px';

    }

    document.getElementById('main').onclick = () => {

        side_menu.style.width = 0;

    }

    list.ondrag = (e) => {

        list.scrollBy(e.deltaY, 0);

    }

    list.onwheel = (e) => {

        side_menu.style.width = 0;

        if(e.deltaY > 0) {

            list.scrollBy(e.deltaY, 0);

        } else {

            list.scrollBy(e.deltaY, 0);

        }

    }

    list.onscroll = () => {

        side_menu.style.width = 0;

    }

    let s_txs;
    side_menu.ontouchstart = (e) => {

        s_txs = e.touches[0].clientX;

    }

    side_menu.ontouchmove = (e) => {

        if(s_txs - e.touches[0].clientX > 50) {
            side_menu.style.width = 0;
        }

    }

    let b_txs;
    body[0].ontouchstart = (e) => {

        b_txs = e.touches[0].clientX;

    }

    body[0].ontouchmove = (e) => {

        if(b_txs <= 50 && (e.touches[0].clientX - b_txs) > 50) {

            side_menu.style.width = '300px';

        }

    }

    const modal_close_buttons = document.getElementsByClassName('close-modal');

    for(let i = 0; i < modal_close_buttons.length; i++) {

        modal_close_buttons[i].onclick = (e) => {

            let modal = modal_close_buttons[i].parentElement.parentElement;
            modal.style.display = 'none';

        }

    }


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

        // TODO: Handle article actions here.
        // Swipe up, mark as done.
        // Tap to edit.

    }

    if ('serviceWorker' in navigator) {

        navigator.serviceWorker.register('service-worker.js');
        console.log('Service-worker registerd!');

    } else {

        console.error('Service-workers not supported.');

    }

}
