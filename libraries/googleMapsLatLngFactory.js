function googleMapsLatLngFactory(array_travel) {

  var heatMapData = [];

  for (let i = 0; i < array_travel.length; i++) {

    const latitude = array_travel[i].latitude;
    const longitude = array_travel[i].longitude;
    const heatMapDataPoint = new google.maps.LatLng(latitude, longitude);
    heatMapData.push(heatMapDataPoint);

  }

  return heatMapData;

}