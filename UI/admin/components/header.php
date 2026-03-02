<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Hệ Thống Thi Trực Tuyến - Admin Portal'; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        
        /* Tùy chỉnh thanh cuộn cho mượt mà */
        .custom-scrollbar::-webkit-scrollbar { 
            width: 5px; 
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track { 
            background: transparent; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb { 
            background: rgba(255,255,255,0.2); 
            border-radius: 10px; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
            background: rgba(255,255,255,0.4); 
        }
        
        /* Smooth transition cho menu accordion */
        .menu-content {
            transition: max-height 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
        }
        .menu-content.open {
            max-height: 500px; /* Số đủ lớn để chứa hết menu con */
        }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden text-slate-800">