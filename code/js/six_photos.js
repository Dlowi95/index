document.addEventListener('click',function(e){
    if(e.target.classList.contains("gallery-item")) {
         const src = e.target.getAttribute("src");
         const imgModal = document.querySelector(".modal-img");
         imgModal.src =src;
         const myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
         myModal.show();
    }
});