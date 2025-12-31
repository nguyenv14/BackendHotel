@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Footer
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-timetable"></i>
                    <span><?php
                    $today = date('d/m/Y');
                    echo $today;
                    ?></span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Bảng Danh Sách Cấu Hình
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <div class="table-responsive">
                <table class="table table-bordered">
                    
                    <tbody>
                       
                        @if ($info == null)
                            <tr>
                                <th>Tên Công Ty/Shop</th>
                                <td contenteditable id="name" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Tổng Đài Chăm Sóc</th>
                                <td contenteditable id="call" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Email Đơn Vị</th>
                                <td contenteditable id="email" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Địa Chỉ</th>
                                <td contenteditable id="dress" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Slogan Công Ty</th>
                                <td contentEditable id="slogan" class="company_edit">
                                    <div class="company_edit" style="width: 660px;overflow: hidden"></div>
                                </td>
                            </tr>
                            <tr>
                                <th>Copyright</th>
                                <td contentEditable id="copyright" class="company_edit">
                                    <div class="company_edit" style="width: 660px;overflow: hidden"></div>
                                </td>
                            </tr>
                        @else
                        <tr>
                            <th>Tên Công Ty/Shop</th>
                            <td contenteditable id="name" class="company_edit">
                                {{ $info->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Tổng Đài Chăm Sóc</th>
                            <td contenteditable id="call" class="company_edit">
                                {{ $info->company_hostline }}</td>
                        </tr>
                        <tr>
                            <th>Email Đơn Vị</th>
                            <td contenteditable id="email" class="company_edit">
                                {{ $info->company_mail }}</td>
                        </tr>
                        <tr>
                            <th>Địa Chỉ</th>
                            <td contenteditable id="dress" class="company_edit">
                                {{ $info->company_address }}</td>
                        </tr>
                        <tr>
                            <th>Slogan Công Ty</th>
                            <td contentEditable id="slogan" class="company_edit">
                                <div class="company_edit" style="width: 660px;overflow: hidden">
                                    {{ $info->company_slogan }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Copyright</th>
                            <td contentEditable id="copyright" class="company_edit">
                                <div class="company_edit" style="width: 660px;overflow: hidden">
                                    {{ $info->company_copyright }}</div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Policy Files Section -->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div class="card-title col-sm-9">Quản Lý File Policy
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                            <i class="mdi mdi-upload"></i> Upload File
                        </button>
                    </div>
                </div>

                <!-- File List -->
                <div id="policyFilesList">
                    <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên File</th>
                                <th>Đường Dẫn</th>
                                <th>Trạng Thái</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="policyFilesTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <span class="ms-2">Đang tải danh sách file...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload File Modal -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Upload File Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadFileForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Chọn File</label>
                            <input type="file" class="form-control" id="fileInput" name="file" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Chỉ chấp nhận file PDF, DOC, DOCX (Tối đa 10MB)</div>
                        </div>
                        <div id="uploadProgress" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                            </div>
                            <p class="text-center mt-2">Đang upload...</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="btnUploadFile">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.table-bordered tbody').on('blur', '.company_edit', function() {
                var name = $('#name').text();
                var call = $('#call').text();
                var email = $('#email').text();
                var dress = $('#dress').text();
                var slogan = $('#slogan').text();
                var copyright = $('#copyright').text();
                $.ajax({
                    url: '{{ url('admin/config-footer/edit-content-footer') }}',
                    method: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_name:name,
                        company_hostline:call,
                        company_mail:email,
                        company_address:dress,
                        company_slogan:slogan,
                        company_copyright:copyright,
                    },

                    success: function(data) {
                        message_toastr("success", "Cập nhập thông tin trang web thành công")
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            })

            // Upload File Handler
            $('#btnUploadFile').on('click', function() {
                const fileInput = $('#fileInput')[0];
                const file = fileInput.files[0];
                
                if (!file) {
                    alert('Vui lòng chọn file để upload');
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File quá lớn. Vui lòng chọn file nhỏ hơn 10MB');
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Show progress
                $('#uploadProgress').show();
                $('#btnUploadFile').prop('disabled', true);

                $.ajax({
                    url: '{{ url('admin/config-footer/upload-policy-file') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            message_toastr("success", response.message || "Upload file thành công");
                            $('#uploadFileModal').modal('hide');
                            $('#uploadFileForm')[0].reset();
                            
                            // Reload file list
                            loadPolicyFiles();
                        } else {
                            alert(response.message || 'Upload file thất bại');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Upload file thất bại';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    },
                    complete: function() {
                        $('#uploadProgress').hide();
                        $('#btnUploadFile').prop('disabled', false);
                    }
                });
            });

            // Function to load policy files
            function loadPolicyFiles() {
                $.ajax({
                    url: '{{ url('admin/config-footer/policy-files') }}',
                    method: 'GET',
                    success: function(response) {
                        const tbody = $('#policyFilesTableBody');
                        tbody.empty();
                        
                        if (response.success && response.data && response.data.length > 0) {
                            response.data.forEach(function(file, index) {
                                let parsedBadge = '';
                                let parseButton = '';
                                
                                if (file.parsed) {
                                    // Đã Parse
                                    parsedBadge = '<span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Đã Parse</span>';
                                    parseButton = '<button type="button" class="btn btn-sm btn-success btn-parse-file" disabled>' +
                                        '<i class="mdi mdi-file-document-edit"></i> Parse File</button>';
                                } else if (file.parsing) {
                                    // Đang Parse
                                    parsedBadge = '<span class="badge bg-info"><i class="mdi mdi-loading mdi-spin"></i> Đang Parse...</span>';
                                    parseButton = '<button type="button" class="btn btn-sm btn-success btn-parse-file" disabled>' +
                                        '<i class="mdi mdi-loading mdi-spin"></i> Đang Parse...</button>';
                                } else {
                                    // Chưa Parse
                                    parsedBadge = '<span class="badge bg-warning"><i class="mdi mdi-clock-outline"></i> Chưa Parse</span>';
                                    parseButton = '<button type="button" class="btn btn-sm btn-success btn-parse-file" ' +
                                        'data-file-url="' + file.url + '" ' +
                                        'data-file-name="' + file.name + '" ' +
                                        'data-file-path="' + file.path + '">' +
                                        '<i class="mdi mdi-file-document-edit"></i> Parse File</button>';
                                }
                                
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${file.name}</td>
                                        <td><small class="text-muted">${file.path}</small></td>
                                        <td>${parsedBadge}</td>
                                        <td>
                                            <a href="${file.url}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-download"></i> Tải Xuống
                                            </a>
                                            ${parseButton}
                                        </td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                            
                            // Start auto-refresh if there are files being parsed
                            const hasParsingFiles = tbody.find('.badge.bg-info').length > 0;
                            if (hasParsingFiles) {
                                startAutoRefresh();
                            } else {
                                // Stop auto-refresh if no files are parsing
                                if (autoRefreshInterval) {
                                    clearInterval(autoRefreshInterval);
                                    autoRefreshInterval = null;
                                }
                            }
                        } else {
                            tbody.append('<tr><td colspan="5" class="text-center text-muted">Chưa có file nào được upload</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Lỗi khi tải danh sách file:', xhr);
                        const tbody = $('#policyFilesTableBody');
                        tbody.empty();
                        tbody.append('<tr><td colspan="5" class="text-center text-danger">Lỗi khi tải danh sách file</td></tr>');
                    }
                });
            }

            // Load policy files when page loads
            loadPolicyFiles();

            // Auto-refresh file list every 5 seconds if there are files being parsed
            let autoRefreshInterval = null;
            function startAutoRefresh() {
                if (autoRefreshInterval) return;
                autoRefreshInterval = setInterval(function() {
                    // Check if there are any files with parsing status
                    const hasParsingFiles = $('#policyFilesTableBody').find('.badge.bg-info').length > 0;
                    if (hasParsingFiles) {
                        loadPolicyFiles();
                    } else {
                        // Stop auto-refresh if no files are parsing
                        if (autoRefreshInterval) {
                            clearInterval(autoRefreshInterval);
                            autoRefreshInterval = null;
                        }
                    }
                }, 5000); // Refresh every 5 seconds
            }

            // Parse File Handler
            $(document).on('click', '.btn-parse-file', function() {
                const btn = $(this);
                if (btn.prop('disabled')) {
                    return; // Prevent clicking if already disabled
                }
                
                const fileUrl = btn.data('file-url');
                const fileName = btn.data('file-name');
                const filePath = btn.data('file-path');
                
                if (!fileUrl) {
                    alert('Không tìm thấy URL file');
                    return;
                }
                
                // Disable button and show loading
                btn.prop('disabled', true);
                const originalHtml = btn.html();
                btn.html('<i class="mdi mdi-loading mdi-spin"></i> Đang Parse...');
                
                // Call Laravel endpoint to parse file
                $.ajax({
                    url: '{{ url('admin/config-footer/parse-policy-file') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        file_url: fileUrl,
                        file_name: fileName,
                        file_path: filePath
                    },
                    success: function(response) {
                        if (response.success) {
                            message_toastr("success", response.message || "Đã thêm job parse file vào queue");
                            // Reload file list to update status
                            loadPolicyFiles();
                            // Start auto-refresh to check parsing status
                            startAutoRefresh();
                        } else {
                            alert(response.message || 'Parse file thất bại');
                            btn.prop('disabled', false);
                            btn.html(originalHtml);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Parse file thất bại';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                    }
                });
            });

        });
    </script>
@endsection
