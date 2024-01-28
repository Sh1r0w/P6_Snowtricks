window.addEventListener('scroll', (event) => {
    let scrollY = this.scrollY;
    let icon = document.querySelector('#iconDown')
    let iconUp = document.querySelector('#iconUp')

    if (scrollY >= 1) {
        icon.style.display = 'none'
        iconUp.style.display ='block'
    } else if (scrollY === 0) {
        icon.style.display = 'block'
        iconUp.style.display = 'none'
    }

    

  });