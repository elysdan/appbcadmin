@extends(activeTemplate() .'layouts.master')

@push('css')
    <link rel="stylesheet" href="{{asset('assets/admin/css/simplemde.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/ticket.css')}}">

    <style>
        button.custom-style {
            margin-bottom: 20px;
        }
    </style>
@endpush
@section('content')
    @include(activeTemplate() .'partials.front_br')


    <div class="contact">


        <div class="get-in-touch">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-8">
                        <div class="section-title">
                            <h2>@lang('Ticket') #{{$my_ticket->ticket}}</h2>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-12 mb-30">


                        <div class="card">


                            <div class="card-header" style="background-color: #ffffff">
                                <h6 class="card-title float-left">@lang('Subject'): {{ $my_ticket->subject }}</h6>


                                <div class="float-right">

                                    @if($my_ticket->status == 0)
                                        <span class="badge badge-primary "> @lang('Open') </span>
                                    @elseif($my_ticket->status == 1)
                                        <span class="badge badge-success "> @lang('Answered') </span>
                                    @elseif($my_ticket->status == 2)
                                        <span class="badge badge-info"> @lang('Customer Replied') </span>
                                    @elseif($my_ticket->status == 3)
                                        <span class="badge badge-danger "> @lang('Closed') </span>
                                    @endif

                                </div>
                            </div>
                            <div class="card-body">




                                <div class="accordion" id="accordionExample">

                                    <div class="card">
                                        <div class="card-header card-header-bg" style="background-color: #ffffff" id="headingThree">
                                            <h2 class="my-1 ">
                                                <a class="btn btn-link collapsed float-left "
                                                   href="javascript:void(0)" data-toggle="collapse"
                                                   data-target="#collapseThree" aria-expanded="true"
                                                   aria-controls="collapseThree">
                                                    <i class="fa fa-pencil"></i> @lang('Reply')
                                                </a>


                                                <a class="btn btn-link collapsed float-right accor"
                                                   href="javascript:void(0)" data-toggle="collapse"
                                                   data-target="#collapseThree" aria-expanded="true"
                                                   aria-controls="collapseThree">
                                                    <i class="fa fa-plus"></i>
                                                </a>

                                            </h2>
                                        </div>
                                        <div id="collapseThree" class="collapse show" aria-labelledby="headingThree"
                                             data-parent="#accordionExample">

                                            <div class="card-body">

                                                <form method="post"
                                                      action="{{ route('user.supportticket.reply', [$my_ticket->id,$my_ticket->viewpin]) }}"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                            <textarea name="message"
                                                                                      class="form-control form-control-lg"
                                                                                      id="inputMessage"
                                                                                      placeholder="@lang('Your Reply') ..."
                                                                                      rows="4" cols="10"></textarea>
                                                            </div>

                                                            <div class="row">
                                                                <div class=" col-md-12">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="inputAttachments">@lang('Attachments')</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <div class="form-group">
                                                                        <input type="file"
                                                                               name="attachments[]"
                                                                               id="inputAttachments"
                                                                               class="form-control"/>
                                                                        <div
                                                                            id="fileUploadsContainer"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <a href="javascript:void(0)"
                                                                           class="btn btn-danger btn-round"
                                                                           onclick="extraTicketAttachment()">
                                                                            <i class="fa fa-plus"></i> @lang('Add More')
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 ">
                                                                    <div
                                                                        class="form-group ticket-attachments-message text-muted">
                                                                        @lang("Allowed File Extensions: .jpg, .jpeg, .png, .pdf")
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row justify-content-end">

                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                            class="btn btn-danger custom-danger delete_button custom-style"
                                                                            data-toggle="modal"
                                                                            data-target="#DelModal">
                                                                        <i class="fa fa-times"></i> @lang('Close')
                                                                    </button>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="submit"
                                                                            class="btn btn-success custom-success custom-style"
                                                                            name="replayTicket" value="1">
                                                                        <i class="fa fa-paper-plane"></i> @lang('Send')
                                                                    </button>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>


                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12 product-service md-margin-bottom-30">
                                        <ol class="commentlist noborder nomargin  clearfix">
                                            @foreach($messages as $message)
                                                @if($message->admin_id == 0)
                                                    <div class="row">
                                                        <div class="col-md-10 offset-md-2">
                                                            <li class="comment even thread-even depth-1"
                                                                id="li-comment-1">
                                                                <div id="comment-1"
                                                                     class="comment-wrap clearfix">
                                                                    <div class="comment-content clearfix">
                                                                        <div
                                                                            class="comment-author">{{ $message->ticket->name }}
                                                                            <span>{{ date('d F, Y - h:i A', strtotime($message->created_at)) }}</span>
                                                                        </div>
                                                                        <p>{{ $message->message }}</p>

                                                                        @if($message->attachments()->count() > 0)
                                                                            <div class="mt-2">
                                                                                @foreach($message->attachments as $k=>$image)
                                                                                    <a href="{{route('user.supportticket.download',encrypt($image->id))}}"
                                                                                       class="ml-4"><i
                                                                                            class="fa fa-file-text-o"></i> {{++$k}} @lang('File Download')
                                                                                    </a>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif



                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                            </li>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            <li class="comment even thread-even depth-1"
                                                                id="li-comment-1">
                                                                <div id="comment-1"
                                                                     class="comment-wrap clearfix">
                                                                    <div class="comment-meta">
                                                                        <div class="comment-author vcard">
                                                                <span class="comment-avatar clearfix">
                                                                    <img alt=""
                                                                         src="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"
                                                                         class="avatar avatar-60 photo avatar-default"
                                                                         width="60" height="60"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="comment-content clearfix">
                                                                        <div class="comment-author">
                                                                            @lang('Admin')
                                                                            <span>{{date('d F, Y - h:i A',strtotime($message->created_at)) }}</span>
                                                                        </div>
                                                                        <p>{{ $message->message }}</p>

                                                                        @if($message->attachments()->count() > 0)
                                                                            <div class="mt-2">
                                                                                @foreach($message->attachments as $image)
                                                                                    <a href="{{route('user.supportticket.download',encrypt($image->id))}}"
                                                                                       class="ml-4 btn btn-sm btn-success">
                                                                                        <i class="fa fa-download"></i></a>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                            </li>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </ol>
                                    </div>

                                </div>


                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- contact end -->






    <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="post" action="{{ route('user.supportticket.reply', [$my_ticket->id,$my_ticket->viewpin]) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <strong class="modal-title"><i class='fa fa-exclamation-triangle'></i> @lang('Confirmation')!</strong>
                        <button type="button" class="close btn btn-sm" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <strong>@lang('Are you sure you want to Close This Support Ticket')?</strong>
                    </div>
                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success btn-sm" name="replayTicket"
                                value="2"><i class="fa fa-check"></i> @lang("Confirm")
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i>
                            @lang('Close')
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>





@endsection

@push('js')

    <script src="{{asset('assets/admin/js/simplemde.min.js')}}"></script>
    <script>
        var simplemde = new SimpleMDE({element: document.getElementById("inputMessage")});

        $(document).ready(function () {
            $('.card-body').scrollTop($('.card-body')[0].scrollHeight);
            $('.delete-message').on('click', function (e) {
                $('.message_id').val($(this).data('id'));
            })

        });

        function extraTicketAttachment() {
            $("#fileUploadsContainer").append('<input type="file" name="attachments[]" class="form-control mt-1" required />')
        }
    </script>
@endpush
