@extends('layouts.admin.app')

@section('title',translate('Referral Income Setting'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title text-capitalize">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{asset('/public/assets/admin/img/point.png')}}" alt="{{ translate('loyalty_point') }}" class="width-24">
                </div>
                <span>
                    {{translate('Refferral_income_setting')}}
                </span>
            </h1>
        </div>

        
        
        <div class="card mt-3">
            <div class="card-header text-capitalize border-0">
                <div class="btn--container justify-content-end">
                    <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i class="tio-filter-list mr-1"></i>{{translate('Add')}}</button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                           class="table table-thead-bordered table-align-middle card-table table-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">{{ translate('sl') }}</th>
                                <th class="text-center">{{translate('Level')}}</th>
                                <th class="text-center">{{translate('Level Name')}}</th>
                                <th class="text-center">{{translate('Income Percentage')}}</th>
                                <th class="text-center">{{translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody id="Table-body">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Level</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:" method="post" id="add_new_level_form" class="row g-2">
                        @csrf
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body pt-2">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('Level_name')}}</label>
                                        <input type="text" name="level_name" id="new_level_name" class="form-control" placeholder="{{translate('level_name')}}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('Enter_Percentage_of_new_level')}}</label>
                                        <input type="number" name="percentage" id="new_percentage" class="form-control" placeholder="{{translate('Percentage')}}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <a class="btn btn--info min-w-120px" data-dismiss="modal">{{translate('Close')}}</a>
                                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        var data = 'level_nikalo';

        $.ajax({
            url:"{{route('admin.customer.loyalty-point.show_level')}}",
            type:'POST',
            data:{
            "_token": "{{ csrf_token() }}",
            data: data
            },
            success:function(response){
            var subdata = [];
            
                if (response == '') {
                    $('#Table-body').html('');
                    console.log('empty');
                }else{

                    $('#Table-body').html('');
                    var slno = 1;
                    response.forEach(element => {
                        subdata += "<tr><td class='text-center'>"+slno+"</td><td class='text-center'>"+element.Level+"</td><td class='text-center'>"+element.level_name+"</td><td class='text-center'>"+element.percentage+" %</td><td><div class='btn--container justify-content-center'><button class='action-btn' data-toggle='modal' data-target='#exampleModal-"+element.id+"'><i class='tio-edit'></i></i></button><div class='delete-button'  data-id='"+element.id+"'></div></div><div class='modal fade' id='exampleModal-"+element.id+"' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'><div class='modal-dialog modal-dialog-centered' role='document'><div class='modal-content'><div class='modal-header'><h5 class='modal-title' id='exampleModalLabel'>Edit "+element.level_name+" Level</h5><button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div><div class='modal-body'><div class='col-lg-12'><div class='card'><div class='card-body pt-2'><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Level_name')}}</label><input type='text' name='level_name' id='level_name' value='"+element.level_name+"' class='form-control level_name' placeholder='{{translate('level_name')}}' required></div><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Enter_Percentage')}}</label><input type='number' name='percentage' id='percentage' value='"+element.percentage+"' class='form-control percentage' placeholder='{{translate('Percentage')}}' required></div><input type='hidden' name='level_id' id='level_id' class='level_id' value='"+element.id+"'></div></div></div><div class='col-12'><div class='btn--container justify-content-end'><a class='btn btn--info min-w-120px' data-dismiss='modal'>{{translate('Close')}}</a><button type='submit' class='btn btn--primary submit-edit-modal'>{{translate('submit')}}</button></div></div></div></div></div></div></td></tr>";

                        slno++;
                    });
                    
                    $('#Table-body').html(subdata);

                    $(document).find('tr:last-child').children('td:last-child').children('.btn--container').children('.delete-button').html("<button class='action-btn btn--danger btn-outline-danger close_button'><i class='tio-delete-outlined'></i></button>");
                }
            }
        })
    </script>

    <script>
        $(document).find('tr:last-child').children('td:last-child').children('.btn--container').children('.delete-button').html("<button class='action-btn btn--danger btn-outline-danger close_button'><i class='tio-delete-outlined'></i></button>");


        $(document).on('click','.close_button' , function(){
            var deleted_id = $(this).parents('.delete-button').attr('data-id');
            delete_alert('Are You Sure');

            function delete_alert(message) {
                Swal.fire({
                    title: '{{translate("Delete Alert?")}}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#01684b',
                    cancelButtonText: '{{translate("No")}}',
                    confirmButtonText: '{{translate("Yes")}}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:"{{route('admin.customer.loyalty-point.delete_level')}}",
                            type:'POST',
                            data:{
                            "_token": "{{ csrf_token() }}",
                            data: deleted_id
                            },
                            success:function(response){
                                toastr.success('{{ translate("Level Deleted successfully!") }}');
                                location.reload();
                            }
                        }) 
                    }
                })
            }

            // $.ajax({
            //     url:"{{route('admin.customer.loyalty-point.show_level')}}",
            //     type:'POST',
            //     data:{
            //     "_token": "{{ csrf_token() }}",
            //     data: data
            //     },
            //     success:function(response){
            //     var subdata = [];
                
            //         if (response == '') {
            //             $('#Table-body').html('');
            //             console.log('empty');
                        
            //         }else{

            //             $('#Table-body').html('');
            //             var slno = 1;
            //             response.forEach(element => {
            //                 subdata += "<tr><td class='text-center'>"+slno+"</td><td class='text-center'>"+element.Level+"</td><td class='text-center'>"+element.level_name+"</td><td class='text-center'>"+element.percentage+"</td><td><div class='btn--container justify-content-center'><button class='action-btn' data-toggle='modal' data-target='#exampleModal-"+element.id+"'><i class='tio-edit'></i></i></button><div class='delete-button'  data-id='"+element.id+"'></div></div><div class='modal fade' id='exampleModal-"+element.id+"' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'><div class='modal-dialog modal-dialog-centered' role='document'><div class='modal-content'><div class='modal-header'><h5 class='modal-title' id='exampleModalLabel'>Edit "+element.level_name+" Level</h5><button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div><div class='modal-body'><div class='col-lg-12'><div class='card'><div class='card-body pt-2'><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Level_name')}}</label><input type='text' name='level_name' id='level_name'  class='form-control level_name' placeholder='{{translate('level_name')}}' required></div><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Enter_Percentage')}}</label><input type='number' name='percentage' id='percentage percentage' class='form-control' placeholder='{{translate('Percentage')}}' required></div><input type='hidden' name='level_id' id='level_id' class='level_id' value='"+element.id+"' required></div></div></div><div class='col-12'><div class='btn--container justify-content-end'><a href='' class='btn btn--info min-w-120px' data-dismiss='modal'>{{translate('Close')}}</a><button type='submit' class='btn btn--primary'>{{translate('submit')}}</button></div></div></div></div></div></div></td></tr>";

            //                 slno++;
            //             });
                        
            //             $('#Table-body').html(subdata);

            //             $(document).find('tr:last-child').children('td:last-child').children('.btn--container').children('.delete-button').html("<button class='action-btn btn--danger btn-outline-danger close_button'><i class='tio-delete-outlined'></i></button>");
                        
            //         }
            //     }
            // })
        });
    </script>

    <script>
        $(document).on('click', '.submit-edit-modal' , function(){
            var level_id = $(this).parents('.modal-body').children('.col-lg-12').children('.card').children('.card-body').children('.level_id').val();
            var level_name = $(this).parents('.modal-body').children('.col-lg-12').children('.card').children('.card-body').children('.form-group').children('.level_name').val();
            var percentage = $(this).parents('.modal-body').children('.col-lg-12').children('.card').children('.card-body').children('.form-group').children('.percentage').val();

            $.ajax({
                url:"{{route('admin.customer.loyalty-point.edit_level')}}",
                type:'POST',
                data:{
                    "_token": "{{ csrf_token() }}",
                    level_id: level_id,
                    level_name: level_name,
                    percentage: percentage,
                },
                success:function(response){
                    toastr.success('{{ translate("Level Edited successfully!") }}');
                    location.reload();
                }
            })
        })
    </script>

<script>

    $('#add_new_level_form').on('submit', function () {

        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: "{{route('admin.customer.loyalty-point.Add_level')}}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    toastr.success('{{ translate("Level Added successfully!") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    $('#exampleModal').modal('hide');
                    $('#new_percentage').val('');
                    $('#new_level_name').val('');
                    
                    var data = 'level_nikalo';

                    $.ajax({
                        url:"{{route('admin.customer.loyalty-point.show_level')}}",
                        type:'POST',
                        data:{
                        "_token": "{{ csrf_token() }}",
                        data: data
                        },
                        success:function(response){
                        var subdata = [];
                        
                            if (response == '') {
                                $('#Table-body').html('');
                                console.log('empty');
                            }else{

                                $('#Table-body').html('');
                                var slno = 1;
                                response.forEach(element => {
                                    subdata += "<tr><td class='text-center'>"+slno+"</td><td class='text-center'>"+element.Level+"</td><td class='text-center'>"+element.level_name+"</td><td class='text-center'>"+element.percentage+"</td><td><div class='btn--container justify-content-center'><button class='action-btn' data-toggle='modal' data-target='#exampleModal-"+element.id+"'><i class='tio-edit'></i></i></button><div class='delete-button' data-id='"+element.id+"'></div></div><div class='modal fade' id='exampleModal-"+element.id+"' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'><div class='modal-dialog modal-dialog-centered' role='document'><div class='modal-content'><div class='modal-header'><h5 class='modal-title' id='exampleModalLabel'>Edit "+element.level_name+" Level</h5><button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div><div class='modal-body'><div class='col-lg-12'><div class='card'><div class='card-body pt-2'><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Level_name')}}</label><input type='text' name='level_name' id='level_name' value='"+element.level_name+"' class='form-control level_name' placeholder='{{translate('level_name')}}' required></div><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Enter_Percentage')}}</label><input type='number' name='percentage' id='percentage' value='"+element.percentage+"' class='form-control percentage' placeholder='{{translate('Percentage')}}' required><input type='hidden' name='level_id' id='level_id' class='level_id' value='"+element.id+"'></div></div></div></div><div class='col-12'><div class='btn--container justify-content-end'><a class='btn btn--info min-w-120px' data-dismiss='modal'>{{translate('Close')}}</a><button type='submit' class='btn btn--primary submit-edit-modal'>{{translate('submit')}}</button></div></div></div></div></div></div></td></tr>";

                                    slno++;
                                });
                                
                                $('#Table-body').html(subdata);

                                $(document).find('tr:last-child').children('td:last-child').children('.btn--container').children('.delete-button').html("<button class='action-btn btn--danger btn-outline-danger close_button'><i class='tio-delete-outlined'></i></button>");
                            }
                        }
                    })
                }
            }
        });
    });
</script>


@endpush
