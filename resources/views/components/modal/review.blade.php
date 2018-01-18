<div class="modal fade" id="review-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-gutters">
                    <div class="col col-auto pr-2" id="reviews_rating"></div>
                    <div class="col col-auto pr-2 pt-1">
                        <select id="rating" name="rate">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col" id="reviews_count"> <a></a> reviews</div>
                    <div class="col col-auto py-1"></div>
                </div>
                <hr class="my-4">
                <form onsubmit="return review_submit(this);">
                    <div class="row justify-content-center">
                        <div class="col col-auto">
                            <h4>評分與評論</h4>
                        </div>
                    </div>
                    <div class="row justify-content-center ">
                        <div class="col col-auto">
                            <select id="user-rating" name="rate" required>
                                <option value=""></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center ">
                        <div class="col">
                            <input type="text" name="comment" class="form-control" placeholder="您的評論！">
                        </div>
                    </div>
                    <div class="row justify-content-center ">
                        <div class="col col-auto">
                            <button type="submit" class="btn btn-primary px-4 my-2">提交</button>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="">
                    <div class="row">
                        <div class="col" id="comment_area">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>
var review_modal_show_bs_modal;
var review_modal_hide_bs_modal;
var review_modal_form_submit;
var review_submit = (el) => {
    review_modal_form_submit(el,$('#review-modal'))
    return false;
};
var review_modal_load = (onshow = null, onhide = null, onsubmit = null) => {
    review_modal_form_submit = onsubmit;
    $('#review-modal').off('show.bs.modal', review_modal_show_bs_modal);
    $('#review-modal').off('hide.bs.modal', review_modal_hide_bs_modal)
    review_modal_show_bs_modal = (e) => {
        $('#user-rating').barrating({
            theme: 'fontawesome-stars',
            initialRating: 0,
        })
        $('#rating').barrating({
            theme: 'fontawesome-stars-o',
            initialRating: 0,
            readonly: true,
        });
        if (onshow)
            onshow(e)
    };
    review_modal_hide_bs_modal = (e) => {
        if (onhide)
            onhide(e)
        $('#user-rating').barrating('destroy')
        $('#rating').barrating('destroy')
        $('#user-rating').val("")
        $('[name=comment]').val("")
    };
    $('#review-modal').on('show.bs.modal', review_modal_show_bs_modal);
    $('#review-modal').on('hide.bs.modal', review_modal_hide_bs_modal)
}
</script>
{{$slot}}