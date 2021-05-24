<script>
    $(document).ready(function(){
        // Existing Data Listeners
        addGeneCreationListener($("#geneList").parent().find(".add-genetics-row"));
        addGeneSelectionListener($("#geneList").find(".gene-select"));
        addGeneDeletionListener($("#geneList").find(".delete-genetics-row"));
        $(".gradient-gene-input").change(function() {
            validateGradientInput($(this));
        });

        // Helper Functions
        function addGeneCreationListener($btn) {
            $btn.click(function(e){
                e.preventDefault();
                $clone = $(".genetics-row").clone();
                $clone.removeClass("genetics-row hide");
                $("#geneList").append($clone);
                addGeneSelectionListener($clone.find(".gene-select"));
                addGeneDeletionListener($clone.find(".delete-genetics-row"));
            });
        }
        function addGeneDeletionListener($btn) {
            $btn.click(function(e){
                e.preventDefault();
                $(this).parent().remove();
            });
        }
        function addGeneSelectionListener($select) {
            $select.selectize();
            $select.change(function(){
                var loci = $(this).val();
                var options = $(this).parent().find(".gene-select-options");
                $.ajax({
                    type: "GET", url: "{{ url('admin/masterlist/check-genes') }}?loci="+loci, dataType: "text"
                }).done(function (res) {
                    options.html(res);
                    options.find(".allele-select").selectize();
                    options.find(".gradient-gene-input").change(function(e){
                        validateGradientInput($(this));
                    });
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    alert("AJAX call failed: " + textStatus + ", " + errorThrown);
                });
            });
        }
        function validateGradientInput($input) {
            var val = $input.val();
            while(val.length < $input.attr('maxlength')) val += "-";
            $input.val(val.replace(/[1]/g,"+").replace(/[^-+]/g,"-"));
        }
    });
</script>
