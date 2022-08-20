<!DOCTYPE html>
<html  dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}"  >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{asset('/images/rawLogo.png')}}">
        <title>Baitoutee بيتوتي</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <!-- Styles -->
        <!-- Bootstrap 4 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased" style="text-align: start">
        <div class="relative flex items-top justify-center min-h-screen  sm:items-center py-4 sm:pt-0" style="background-color:#c3e2eb "  dir="rtl">

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center pt-8  sm:pt-0">
                    <img src="{{asset('/images/logo.png')}}" class="img-fluid" style="max-width: 60%">
                </div>
                <h5 style="text-align: center;color:#2B9694;margin:15px;">
                    منصة بيتوتي لطلب الوجبات المنزلية وتوصيلها
                </h5>
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">

                        <div class="p-6 border-t light:border-white-700 md:border-l " style="background-color: #2B9694">
                            <div class="flex items-center">
                                <svg fill="none" stroke="currentColor"  viewBox="0 0 24 24" class="w-8 h-8 text-gray-500"><path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                                    <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg>
                                    <div class="mr-4 text-lg leading-7 font-semibold "><a href="/download-student-app" class="underline text-gray-900 dark:text-white">تطبيق طلاب السكن الجامعي  </a></div>
                            </div>
                            <div class="ml-12">
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                    منصة بيتوتي تتضمن تطبيقاً لطلاب السكن الجامعي لتصفح الوجبات المنزلية والاشتراكات المعدة من قبل طهاة المنصة والقيام بإجراء طلب توصيل للوجبات عبر المنصة.
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-light-200 light:border-white-700 " style="background-color: #2B9694">
                            <div class="flex items-center">
                                <svg fill="none" stroke="currentColor"  viewBox="0 0 24 24" class="w-8 h-8 text-gray-500"><path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                                    <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg>
                                    <div class="mr-4 text-lg leading-7 font-semibold"><a href="/download-chef-app" class="underline text-gray-900 dark:text-white"> تطبيق الطهاة المنزليين </a></div>
                            </div>
                            <div class="ml-12">
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                    منصة بيتوتي تتضمن تطبيقاً للطهاة المنزليين لعرض قائمة وجباتهم وبرامج اشتراكاتهم ومتابعة عمليات الطلبات الواصلة إليهم.
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-light-200 light:border-white-700  md:border-l" style="background-color: #2B9694">
                            <div class="flex items-center">
                                <svg fill="none" stroke="currentColor"  viewBox="0 0 24 24" class="w-8 h-8 text-gray-500"><path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                                    <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg>
                                    <div class="mr-4 text-lg leading-7 font-semibold"><a href="/download-delivery-app" class="underline text-gray-900 dark:text-white"> تطبيق عمال التوصيل </a></div>
                            </div>
                            <div class="ml-12">
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm "  >
                                      منصة بيتوتي تتضمن تطبيقاً لعمال التوصيل لمتابعة عمليات التوصيل الموكلة إليهم وتغيير حالتها.
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-light-200 light:border-white-700 "style="background-color: #2B9694">
                            <div class="flex items-center" >
                                <svg fill="none" stroke="currentColor"  viewBox="0 0 24 24" class="w-8 h-8 text-gray-500" >
                                    <path d="M0 4s0-2 2-2h12s2 0 2 2v6s0 2-2 2h-4c0 .667.083 1.167.25 1.5H11a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1h.75c.167-.333.25-.833.25-1.5H2s-2 0-2-2V4zm1.398-.855a.758.758 0 0 0-.254.302A1.46 1.46 0 0 0 1 4.01V10c0 .325.078.502.145.602.07.105.17.188.302.254a1.464 1.464 0 0 0 .538.143L2.01 11H14c.325 0 .502-.078.602-.145a.758.758 0 0 0 .254-.302 1.464 1.464 0 0 0 .143-.538L15 9.99V4c0-.325-.078-.502-.145-.602a.757.757 0 0 0-.302-.254A1.46 1.46 0 0 0 13.99 3H2c-.325 0-.502.078-.602.145z"/></svg>
                                    <div class="mr-4 text-lg leading-7 font-semibold"><a href="{{url('admin')}}" class="underline text-gray-900 dark:text-white">لوحة التحكم</a></div>
                            </div>
                            <div class="ml-12">
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm" >
                                   منصة بيتوتي تتضمن لوحة تحكم للإشراف على عمليات الطلب والتوصيل.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-deck mt-5">
                    <div class="card text-center">
                      <img class="card-img-top mt-5" src="{{asset('/images/student_app_qr.png')}}" alt="Card image cap">
                      <div class="card-body">
                        <h5 class="card-title">تطبيق طلاب السكن الجامعي</h5>
                        <p class="card-text">امسح الرمز السابق لتحميل التطبيق</p>
                      </div>
                      <div class="card-footer">
                        <small class="text-muted">powered by Baitoutee team</small>
                      </div>
                    </div>
                    <div class="card text-center">
                        <img class="card-img-top mt-5" src="{{asset('/images/chef_app_qr.png')}}" alt="Card image cap">
                        <div class="card-body">
                          <h5 class="card-title">تطبيق الطهاة </h5>
                          <p class="card-text">امسح الرمز السابق لتحميل التطبيق</p>
                        </div>
                        <div class="card-footer">
                          <small class="text-muted">powered by Baitoutee team</small>
                        </div>
                      </div>
                      <div class="card text-center">
                        <img class="card-img-top mt-5" src="{{asset('/images/delivery_app_qr.png')}}" alt="Card image cap">
                        <div class="card-body">
                          <h5 class="card-title">تطبيق طلاب عمال التوصيل </h5>
                          <p class="card-text">امسح الرمز السابق لتحميل التطبيق</p>
                        </div>
                        <div class="card-footer">
                          <small class="text-muted">powered by Baitoutee team</small>
                        </div>
                      </div>
                  </div>

            </div>
        </div>
    </body>
</html>
