@if($entry->status=='pending')
<a href="#" class="btn btn-xs btn-success m-1" onclick="approve({{ $entry->id }})">
    {{trans('adminPanel.actions.accept')}}
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
        class="bi bi-check-lg" viewBox="0 0 16 16">
        <path
            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />

    </svg>
</a>
<a href="#" class="btn btn-xs btn-danger m-1" onclick="reject({{ $entry->id }})">
    {{trans('adminPanel.actions.reject')}}
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
        class="bi bi-x" viewBox="0 0 16 16">
        <path
            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
    </svg>
</a>
@endif


<script>
    function approve(id) {
        //send request of approval
        var xhr = new XMLHttpRequest();
        var csrfToken = "{{ csrf_token() }}";
        xhr.open('POST', `/admin/new-orders/${id}/approve`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        xhr.onload = function() {
            if (this.status == 200) {
                //notify
                new Noty({
                    type: "success",
                    text: '{{trans('adminPanel.messages.order_approved')}}',
                }).show();
                //refresh table
                crud.table.ajax.reload();
            } else {
                console.console.log('failed ' + this.status);
            }
        }
        xhr.send();

    }

    function reject(id) {
        //send request of approval
        var xhr = new XMLHttpRequest();
        var csrfToken = "{{ csrf_token() }}";
        xhr.open('POST', `/admin/new-orders/${id}/reject`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        xhr.onload = function() {
            if (this.status == 200) {
                //notify
                new Noty({
                type: "success",
                text: '{{trans('adminPanel.messages.order_rejected')}}',
                }).show();
                //refresh table
                crud.table.ajax.reload();
            } else {
                console.console.log('failed ' + this.status);
            }
        }
        xhr.send();

    }
</script>
