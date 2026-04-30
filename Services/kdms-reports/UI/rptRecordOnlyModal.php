
<div class="modal fade" id="RecordModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog kdms-modal" role="document">
        <div class="modal-content">
            
                <div class="modal-body">
                    <input type = "text" id="devoteeKey" style="display: none;"  name="devoteeKey" value="" onchange="handleChange(this)">
                    <iframe id="myFrame" frameborder="0" marginwidth="0" marginheight="0" scrolling="NO" width="100%" height="100%" src=""></iframe>
                    <script>
                        function handleChange(elem){
                            alert(elem.value);
                            myFrame.setAttribute("src", elem.value);
                            console.log('iframe.contentWindow =', myFrame.contentWindow);    
                        }
                        //var myFrame = document.createElement("IFRAME");
                        //var html = '<body>Foo</body>';
                        //myFrame.setAttribute("src",  document.getElementById("devoteeKey").value);
                        //myFrame.setAttribute("src",  devoteeKey.value);
                        alert(devoteeKey.value);
                        myFrame.setAttribute("src",  "../test.php?a=3948t4");
                        //myFrame.location.reload(true);
                        //document.body.appendChild(myFrame);
                        console.log('iframe.contentWindow =', myFrame.contentWindow);
                        //document.getElementById("myFrame").location.assign(document.getElementById("devoteeKey").value);
                        //document.getElementById("myFrame").location.reload(true);
                    </script>
                </div>
                <div class="modal-footer">
                   <!-- <input type="hidden" name="requestType" id="requestType" value="upsertRemark">
                    <input type="hidden" name="eventId" id="eventId" value="">
                    <input type="hidden" name="userId" id="userId" value="">   
                    <input type="hidden" name="remark_type" id="remark_type" value="">   
                    <input type="hidden" id="devotee_key" name="devotee_key" value=""> 
                <button id="save-amenity" type="button" class="btn btn-primary"
                        onclick="submitRemark('#remarkForm'); return false;">Submit Remark</button>                    -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                </div>
            
        </div>
    </div>
</div>

