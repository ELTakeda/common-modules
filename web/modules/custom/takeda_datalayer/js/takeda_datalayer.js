(function (Drupal, drupalSettings, once) {
  Drupal.behaviors.takedaDatalayer = {
    attach: function (context, settings) {
      once('takedaDataLayer', 'html', context).forEach( function (element) {
          try {
            var gaXhr = new XMLHttpRequest();
            gaXhr.open("GET", "/takeda-datalayer/session", false);
            gaXhr.send(null);
            var gaData = JSON.parse(gaXhr.responseText);

            if (gaData.hasSession) {
                if (gaData.gtm && isGuid(gaData.digitalId)) {
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        'digitalID': gaData.digitalId,
			                  'customerId': gaData.customerId
                    });
                }
                if (gaData.mtm && isGuid(gaData.digitalId)) {
                    window._mtm = window._mtm || [];
                    window._mtm.push({
                        'digitalID': gaData.digitalId,
			                  'customerID': gaData.customerId
                    });
                }
            }
        } catch (error) {
            console.error(error);
        }
      })
    }
  }
} (Drupal, drupalSettings, once));

function isGuid(stringToTest) {
    if (stringToTest[0] === "{") {
        stringToTest = stringToTest.substring(1, stringToTest.length - 1);
    }
    var regexGuid = /^(\{){0,1}[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}(\}){0,1}$/gi;
    return regexGuid.test(stringToTest);
}