<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="http://maps.google.com/maps/api/js?key=AIzaSyD7VjWzVqSOsCIib_hUQ-mv-ry5wzVWTAg"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.24/gmaps.js"></script>

<div id="mymap" style="height: 400px;"></div>


<script type="text/javascript">


    var locations = <?php print_r(json_encode($locations)) ?>;


    var mymap = new GMaps({

        el: '#mymap',

        lat: 7.8731,

        lng: 80.7718,

        zoom:7

    });


   $.each( locations, function( index, value ){

       mymap.addMarker({

           lat: value.longitude,

           lng: value.latitude,

           title: value.location,

           click: function(e) {

               alert('This is '+value.location+', gujarat from India.');

           }

       });

   });


</script>