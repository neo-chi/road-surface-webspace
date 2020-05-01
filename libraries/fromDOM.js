function fromDOM(object_type) {

  var _returnArray = [];

  // Get impact date from "div.impact-log" html.
  if ('IMPACT' == object_type) {

    const IMPACTS = document.querySelectorAll("div.impact-log");
    for (let i = 0; i < IMPACTS.length; i++) { 
      const entry = IMPACTS[i].innerHTML;
      const obj = JSON.parse(entry);
      _returnArray.push(obj);
    }
    //console.log(_returnArray);

  }

  // Get travel date from "div.travel-log" html.
  if ('TRAVEL' == object_type) {

    const TRAVEL = document.querySelectorAll("div.travel-log");
    for (let i = 0; i < TRAVEL.length; i++) { 

      const entry = TRAVEL[i].innerHTML;
      const obj = JSON.parse(entry);
      _returnArray.push(obj);

    }
    //console.log(_returnArray);
    
  }

  return _returnArray;

}


