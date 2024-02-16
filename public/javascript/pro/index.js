
/**
 * @typedef Pro
 * @property {string} businessName 
 * @property {string} showCategories 
 * @property {string} firstImagePath
 * @property {string} showUrl  
 */


//défilement infini

const observer = new IntersectionObserver(function(entries) {
    const entry = entries[0];
    if(entry.isIntersecting) {
        const list = entry.target.parentElement;
        if(list.childElementCount >= list.dataset.count) {
            return;
        }
        document.getElementById('loader').style.display = '';
        fetch('/find-pros/category-'+list.dataset.category+'/city-'+list.dataset.city+'/offset-'+ list.childElementCount, {
            method: 'GET',
            headers: {
                "Accept": "application/json"
            }
        })
        .then(function(res) {
            if(res.ok) {
                return res.json();
            }
        })
        .then(function(pros) {
            document.getElementById('loader').style.display = 'none';
                pros.forEach(function(pro) {
                    list.append(createCard(pro, list.dataset.categoryname));
                });
                observer.observe(list.lastElementChild);
        })
        .catch(function(error) {
            console.error(error);
        })
        .finally(function() {
            observer.unobserve(entry.target);
        })
    }
});

observer.observe(document.getElementById('pro-listing').lastElementChild);



/**
 * 
 * @param {Pro} pro 
 * @param {string} category_name 
 * @returns {HTMLElement}
 */
function createCard(pro, category_name) {
    const card = document.getElementById('pro-card-template').content.cloneNode(true).firstElementChild;

    card.querySelector('#category-name').innerText = category_name;
    card.querySelector('#business-name').innerText = pro.businessName;
    card.querySelector('#show-categories').innerText = pro.showCategories;
    card.querySelector('#pro-img').setAttribute('src', pro.firstPicturePath);
    card.querySelector('a#show-link').setAttribute('href', pro.showUrl);

    return card;
}



//form à faire

