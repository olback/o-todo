/*
    o-todo
    https://github.com/olback/o-todo

*/

const log = console.log;
const current_year = new Date().getFullYear();

window.onload = () => {

    const year_elements = document.getElementsByClassName('current-year');

    for(let i = 0; i < year_elements.length; i++) {
        year_elements[i].innerHTML = current_year;
    }


    const menu_button = document.getElementById('menu-button');
    const side_menu = document.getElementById('side-menu');
    const main = document.getElementById('main');
    const list = document.getElementById('list');
    const body = document.getElementsByTagName('body');

    menu_button.onclick = () => {

        side_menu.style.width = '300px';

    }

    main.onclick = () => {

        side_menu.style.width = 0;

    }

    list.ondrag = (e) => {
        log(e);
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

}
