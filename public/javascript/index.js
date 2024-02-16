
// import('./functions.js').then((module) => {
//     module.clock(document.getElementById('hour'));
// });

import { OptionSuggestor } from './components/OptionSuggestor.js';
import * as F from './components/functions.js';

const category_loader = F.getTemplate('load-layout').firstElementChild;
document.getElementById('category-loader').append(category_loader);

const city_loader = F.getTemplate('load-layout').firstElementChild;
document.getElementById('city-loader').append(city_loader);
// REFAIRE TOUT CI DESSUS LE LOADER

const homeForm = document.getElementById('home-form');

new OptionSuggestor(
    'category',
    homeForm,
    document.getElementById('category-options-list'),
    'option-layout',
    category_loader,
    '/category-suggest'
    );

new OptionSuggestor(
    'city',
    homeForm,
    document.getElementById('city-options-list'),
    'option-layout',
    city_loader,
    '/city-suggest'
    );


homeForm.addEventListener('submit', function(e) {
    if(homeForm.querySelector('input[type=hidden][name=category]') === null) {
        e.preventDefault();
        homeForm.querySelector('input[name=category]').classList.add('is-invalid');
        document.querySelector('#category-error').innerText = 'Merci d\'indiquer une activité';
    }
    if(homeForm.querySelector('input[type=hidden][name=city]') === null) {
        e.preventDefault();
        homeForm.querySelector('input[name=city]').classList.add('is-invalid');
        document.querySelector('#city-error').innerText = 'Merci de préciser le lieu de votre projet';
    }
});

for(const input of homeForm.querySelectorAll('input')) {
    input.addEventListener('focusin', function(e) {
        e.currentTarget.classList.remove('is-invalid');
        e.currentTarget.classList.add('is-focus');
        document.getElementById(e.currentTarget.getAttribute('name')+'-error').innerText = '';
    });
    input.addEventListener('blur', function(e) {
        e.currentTarget.classList.remove('is-focus');
    })
} 







// homeForm.addEventListener('submit', function(e) {
//     e.preventDefault();
//     const data = {
//         category: document.querySelector('.input-category').getAttribute('value'),
//         city: document.querySelector('.input-city').getAttribute('value')
//     };
//     console.log(data);
//     fetch('/search', {
//         method: 'POST',
//         headers: {
//             "Accept": "application/json",
//             "Content-type": "application/json"
//         },
//         body: JSON.stringify(data)
//     })
//     .then(function(res) {
//         if(res.ok) {
//             return res.json();
//         }
//     })
//     .then(function(data) {
//         console.log(data);
//     })
//     .catch(function(error) {
//         console.error(error);
//     })
// });











// (async () => {
//     (await import('./functions.js')).clock(document.getElementById('hour'));
// })();


const goWork = new Date('October 25, 2022 08:30:00');
F.viewTimeCountBeforeDate(goWork, document.getElementById('hour'));

// F.altern(document.querySelector('#hour'));




// /**
//  * 
//  * @param {{title: string, body: string}} post 
//  * @returns 
//  */
// function createPost(post) {
//     let postDiv = document.createElement('div');
//     postDiv.innerHTML = '<h4>'+post.title+'</h4><p>'+post.body+'</p>';
//     return postDiv;
// }


// const loading = document.createElement('div');
// loading.innerHTML = '<strong>En cours de chargement...</strong>';

// const postsDiv = document.getElementById('posts');
// postsDiv.append(loading);
// try {
//     const res = await fetch('https://jsonplaceholder.typicode.com/posts?_delay=7000&_limit=20', {
//         headers: {
//             "Accept": "application/json"
//         }
//     });

//     if(!res.ok) {
//         throw new Error('erreur du serveur');
//     }
//     else {
//         const posts = await res.json();
//         loading.remove();
//         for(let post of posts) {
//             postsDiv.append(createPost(post));
//         }
//     }
// }
// catch(e) {
//     loading.innerHTML = '<strong>Echec du téléchargement</strong>';
// }



