document.addEventListener('DOMContentLoaded', function(){
    const regBtn = document.querySelector(".js-register");

    regBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const btn = e.target;
      const eventId = btn.dataset.eventId;
      const eventType = btn.dataset.btnType;
      const requestUrl = `https://express.free.beeceptor.com/test`;
      const data = {
        eventId: eventId,
        eventType: eventType,
      };
      var reqOptions = {
        method: "POST",
        body: data,
      };
    
      fetch(requestUrl, reqOptions)
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          console.log(data);
        })
        .catch((err) => console.log(err));
    });
})

