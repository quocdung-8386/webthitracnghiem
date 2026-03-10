-- =====================================================
-- Database Schema for Student Progress Management
-- Database: he_thong_thi_trac_nghiem
-- =====================================================

-- Table: students
-- Stores student information linked to nguoi_dung table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_nguoi_dung INT NOT NULL UNIQUE,
    student_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) DEFAULT '',
    class_name VARCHAR(50) DEFAULT '',
    avatar VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung) ON DELETE CASCADE,
    INDEX idx_student_code (student_code),
    INDEX idx_class (class_name),
    INDEX idx_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: progress
-- Tracks student learning progress
CREATE TABLE IF NOT EXISTS progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    completed_tasks INT DEFAULT 0,
    total_tasks INT DEFAULT 0,
    score DECIMAL(4,2) DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'ĐANG HỌC',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample Data for Testing
-- =====================================================

-- Insert sample students (assuming nguoi_dung table has data)
-- NOTE: Adjust ma_nguoi_dung values based on your existing nguoi_dung data

-- Sample 1: Nguyễn Văn An - CNTT K20A - Vượt tiến độ
INSERT INTO students (ma_nguoi_dung, student_code, name, department, class_name) 
VALUES (1, 'SV2023001', 'Nguyễn Văn An', 'CNTT', 'K20A')
ON DUPLICATE KEY UPDATE name = VALUES(name), department = VALUES(department), class_name = VALUES(class_name);

-- Sample 2: Trần Thị Hoa - CNTT K20A - Chậm tiến độ
INSERT INTO students (ma_nguoi_dung, student_code, name, department, class_name) 
VALUES (2, 'SV2023042', 'Trần Thị Hoa', 'CNTT', 'K20A')
ON DUPLICATE KEY UPDATE name = VALUES(name), department = VALUES(department), class_name = VALUES(class_name);

-- Sample 3: Lê Hoàng Minh - Kinh tế K21 - Hoàn thành tốt
INSERT INTO students (ma_nguoi_dung, student_code, name, department, class_name) 
VALUES (3, 'SV2023115', 'Lê Hoàng Minh', 'Kinh tế', 'K21')
ON DUPLICATE KEY UPDATE name = VALUES(name), department = VALUES(department), class_name = VALUES(class_name);

-- Sample 4: Phạm Anh Tuấn - Ngôn ngữ Anh K19 - Đúng lộ trình
INSERT INTO students (ma_nguoi_dung, student_code, name, department, class_name) 
VALUES (4, 'SV2023204', 'Phạm Anh Tuấn', 'Ngôn ngữ Anh', 'K19')
ON DUPLICATE KEY UPDATE name = VALUES(name), department = VALUES(department), class_name = VALUES(class_name);

-- Sample 5: More test students
INSERT INTO students (ma_nguoi_dung, student_code, name, department, class_name) 
VALUES 
(5, 'SV2023056', 'Vũ Thị Mai', 'CNTT', 'K20A'),
(6, 'SV2023089', 'Hoàng Đình Phong', 'Kinh tế', 'K21'),
(7, 'SV2023123', 'Đỗ Thị Lan', 'Ngôn ngữ Anh', 'K20B'),
(8, 'SV2023156', 'Nguyễn Thanh Hà', 'CNTT', 'K19'),
(9, 'SV2023187', 'Trần Văn Bảo', 'Kinh tế', 'K20A')
ON DUPLICATE KEY UPDATE name = VALUES(name), department = VALUES(department), class_name = VALUES(class_name);

INSERT INTO progress (student_id, completed_tasks, total_tasks, score, status)
SELECT id, 
    FLOOR(5 + RAND() * 11) as completed,
    15 as total,
    ROUND(5 + RAND() * 5, 1) as score,
    CASE 
        WHEN FLOOR(5 + RAND() * 11) / 15 > 0.7 THEN 'VƯỢT TIẾN ĐỘ'
        WHEN FLOOR(5 + RAND() * 11) / 15 < 0.4 THEN 'CHẬM TIẾN ĐỘ'
        ELSE 'ĐÚNG LỘ TRÌNH'
    END as status
FROM students WHERE student_code IN ('SV2023056', 'SV2023089', 'SV2023123', 'SV2023156', 'SV2023187')
ON DUPLICATE KEY UPDATE completed_tasks = FLOOR(5 + RAND() * 11), total_tasks = 15, score = ROUND(5 + RAND() * 5, 1);

-- =====================================================
-- View: Get complete progress with student info
-- =====================================================
CREATE OR REPLACE VIEW v_progress AS
SELECT 
    s.id as student_id,
    s.student_code,
    s.name,
    s.department,
    s.class_name as class,
    s.avatar,
    p.completed_tasks,
    p.total_tasks,
    p.score,
    p.status,
    p.updated_at,
    ROUND((p.completed_tasks / NULLIF(p.total_tasks, 0)) * 100, 0) as percent
FROM students s
INNER JOIN progress p ON s.id = p.student_id;

-- Add progress data for first 4 sample students
INSERT INTO progress (student_id, completed_tasks, total_tasks, score, status)
SELECT id, 12, 15, 8.5, 'VƯỢT TIẾN ĐỘ' FROM students WHERE student_code = 'SV2023001'
ON DUPLICATE KEY UPDATE completed_tasks = 12, total_tasks = 15, score = 8.5, status = 'VƯỢT TIẾN ĐỘ';

INSERT INTO progress (student_id, completed_tasks, total_tasks, score, status)
SELECT id, 6, 15, 6.2, 'CHẬM TIẾN ĐỘ' FROM students WHERE student_code = 'SV2023042'
ON DUPLICATE KEY UPDATE completed_tasks = 6, total_tasks = 15, score = 6.2, status = 'CHẬM TIẾN ĐỘ';

INSERT INTO progress (student_id, completed_tasks, total_tasks, score, status)
SELECT id, 14, 15, 9.1, 'HOÀN THÀNH TỐT' FROM students WHERE student_code = 'SV2023115'
ON DUPLICATE KEY UPDATE completed_tasks = 14, total_tasks = 15, score = 9.1, status = 'HOÀN THÀNH TỐT';

INSERT INTO progress (student_id, completed_tasks, total_tasks, score, status)
SELECT id, 10, 15, 7.4, 'ĐÚNG LỘ TRÌNH' FROM students WHERE student_code = 'SV2023204'
ON DUPLICATE KEY UPDATE completed_tasks = 10, total_tasks = 15, score = 7.4, status = 'ĐÚNG LỘ TRÌNH';

