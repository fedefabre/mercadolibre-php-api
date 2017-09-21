<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>API Mercadolibre</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
    .col-3{float:left !important;}
    select{height:400px;}
    .ready{border:3px solid #0ad80a;}
    .ready:focus{border:3px solid #0ad80a;}
    #confirmarCategoria{position: fixed;bottom: 10px;right: 10px;}
    </style>
  </head>
  <body>

    <div class="container mt-4">
      <div class="row">
        <div class="col col-12">
          <form class="" action="/mercadolibre" method="post" id="categoriesForm">
            <input type="hidden" name="listing" id="listing" value="0">
            <div class="col col-3">
            <div class="form-group">
              <label for="categories">Categories</label>
              <select multiple class="form-control" id="categories" name="categories">
                <?php foreach ($categorias as $categoria){ ?>
                          <option value="<?php echo $categoria["id"]; ?>"><?= $categoria["name"] ?></option>
                <?php } ?>
              </select>
            </div>
            </div>
          </form>
          <a href="#" class="btn btn-success d-none" id="confirmarCategoria">Confirmar</a>
        </div>
      </div>
    </div>

    <script>
    // Create Mercadolibre's category interaction
    $("#categoriesForm").change(function(e) {
      var id_cambio = event.target.id; // Get the actual category
      var remover = function(){ // Create a function to delete selects of children's actual category
        return $("select[id$='"+id_cambio+"']").each(function(){ // Get all selects which ends with actual category id
            if(this.id != id_cambio){ // Remove all except actual category's select
              $(this).parent().parent().remove();
            }
        });
      }
      $.when( remover() ).done(function(){ // Promise: Starts executing when remover function ends
        var new_id = 'sub'+id_cambio; // Create new children category's id
        var parentCat = $( "#"+id_cambio+" option:selected" ).val(); // Obtain the id of the child we want to know their childs
        var url = 'https://api.mercadolibre.com/categories/'+parentCat; // Mercadolibre's URL where we find childrens
        $.getJSON(url, function(data) { // Get childrens categories by given parent from mercadolibre's web
            var cat = Array(data);
            //console.log(cat);
            $.each(cat, function(index, value){
                  // console.log(value.name);
                  // console.log(value.settings.listing_allowed);
                  if(value.settings.listing_allowed){ // Evaluate if this category have publish's permission (Not all categories allow to publicate)
                      $("#"+id_cambio).addClass('ready');
                      $("#listing").val("1"); // Set hidden input true to approve first step of double publication permission
                      $("#confirmarCategoria").removeClass('d-none'); // Show confirm button
                      return false; // If we can publicate, thats all. We show de 'Confirmar''s button
                  }else{ // Else, we display new childrens categoryes to continue selection's process
                      $("#listing").val("0"); // Set hidden input false to revoque first step of double publication permission
                      $("#confirmarCategoria").addClass('d-none'); // Hide confirm button
                      if($("#" + new_id).length == 0) { // Not all categories have same children's quantity so if there is no Select available for print, create a new one
                        var newSelect = '<div class="col col-3"><div class="form-group"><label for="'+new_id+'">'+new_id+'</label><select multiple class="form-control" id="'+new_id+'" name="'+new_id+'">';
                        $("#categoriesForm").append(newSelect); // Append new select at the end of form
                      }
                  }
                  $("#"+new_id).find('option').remove().end(); // Remove actual options of our new select
                  $.each(value.children_categories, function(index, value){
                        var toAppend="";
                        toAppend='<option value="'+value.id+'">'+value.name+'</option>'; // Create new option for each new child
                        //console.log(toAppend);
                        $("#"+new_id).append(toAppend); // Append it to the select
                  });
            });
            if($("#" + new_id).length == 0) { // Continuing the idea of ​​the previous condition, we close the select created before
            $("#categoriesForm").append('</select></div></div>');
            }
        });
      });
    });
    </script>
  </body>
</html>
