const back = document.getElementById('back');
if(back != null) {
  back.addEventListener('click', () => {
    window.history.go(-1);
  });
}
