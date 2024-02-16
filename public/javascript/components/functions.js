/**
 * 
 * @param {object} element 
 */
export function clock(element) {
    setInterval(() => {
        element.innerText = new Date().toLocaleString();
    }, 1000);
};

/**
 * affiche un compte à rebours
 * @param {Date} date  la date cible
 * @param {Element} element l'élément dans lequel on affiche
 */
export function viewTimeCountBeforeDate(date, element) {
    setInterval(function() {
        let intervalInSec = (date.getTime() - (new Date()).getTime()) / 1000;
        let days = Math.floor(intervalInSec / 3600 / 24);
        let hours = Math.floor((intervalInSec / 3600) - (days * 24));
        let min = Math.floor((intervalInSec / 60) - ((days * 24 * 60) + (hours * 60)));
        let sec = Math.floor(intervalInSec - ((days * 24 * 3600) + (hours * 3600) + (min * 60)));
    
        element.innerText = days+' jours '+hours+' : '+min+' : '+sec;
    }, 1000)
};



/**
 * fait clignoter l'élément choisi (avec du blanc)
 * @param {Element} element
 * @return {void}
 */
export function altern(element) {
    const original = element.style.color;
    setInterval(function() {
        if(element.style.color === 'white') {
            element.style.color = original;
        }
        else {
            setTimeout(function() {
                element.style.color = 'white';
            }, 350);
        }
    }, 500)
}


/**
 * 
 * @param {string} url 
 * @param {object} options 
 * @returns
 */
export async function myGetFetch(url) {
    const res = await fetch(url, {
        method: 'GET',
        headers: {
            "Accept": "application/json"
        }
    });
    if(res.ok) {
        return res.json();
    }
    throw new Error('erreur serveur');
}


/**
 * 
 * @param {string} id 
 */
export function getTemplate(id) {
    return document.getElementById(id).content.cloneNode(true);
}