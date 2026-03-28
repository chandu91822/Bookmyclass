#!/usr/bin/env bash
set -u

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

APP_URL="${APP_URL:-http://localhost:8081}"
DB_SERVICE="${DB_SERVICE:-db}"
WEB_SERVICE="${WEB_SERVICE:-web}"
DB_NAME="${DB_NAME:-mydb}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-root123}"

TMP_DIR="${TMP_DIR:-/tmp/bookmyclass_fulltest}"
mkdir -p "$TMP_DIR"

ADMIN_EMAIL="admin.test@example.com"
ADMIN_NAME="admin_test"
ADMIN_PASS="Admin123!"

FACULTY_EMAIL="faculty.test@example.com"
FACULTY_NAME="faculty_test"
FACULTY_PASS="Faculty123!"

STUDENT_EMAIL="student.test@example.com"
STUDENT_NAME="student_test"
STUDENT_PASS="Student123!"

TEST_USER_EMAIL="delete.me@example.com"
TEST_USER_NAME="delete_me_user"
TEST_USER_PASS="DeleteMe123!"

TEST_ROOM="AUTO-TEST-101"
TEST_COURSE_CODE="AUTO-CS-101"
TEST_COURSE_NAME="Automation Course"
ANN_TITLE="Automation Announcement"
ANN_MSG="Automated faculty announcement body"
SUBJECT_NAME="Automation Subject"

BOOK_DATE="$(date -d '+2 day' +%F 2>/dev/null || date -v+2d +%F 2>/dev/null || date +%F)"
BOOK_START="10:00"
BOOK_END="11:00"

PASS_COUNT=0
FAIL_COUNT=0

log() { echo "[full-test] $*"; }
pass() { PASS_COUNT=$((PASS_COUNT+1)); echo "[PASS] $*"; }
fail() { FAIL_COUNT=$((FAIL_COUNT+1)); echo "[FAIL] $*"; }

mysql_exec() {
  local sql="$1"
  docker-compose exec -T "$DB_SERVICE" mysql -u"$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -Nse "$sql"
}

assert_db_equals() {
  local desc="$1"
  local sql="$2"
  local expected="$3"
  local actual
  actual="$(mysql_exec "$sql" 2>/dev/null | tr -d '\r')"
  if [[ "$actual" == "$expected" ]]; then
    pass "$desc"
  else
    fail "$desc (expected='$expected', got='${actual:-<empty>}')"
  fi
}

assert_contains() {
  local desc="$1"
  local file="$2"
  local pattern="$3"
  if grep -q "$pattern" "$file"; then
    pass "$desc"
  else
    fail "$desc (pattern '$pattern' not found in $file)"
  fi
}

assert_not_contains() {
  local desc="$1"
  local file="$2"
  local pattern="$3"
  if grep -qi "$pattern" "$file"; then
    fail "$desc (unexpected pattern '$pattern' found in $file)"
  else
    pass "$desc"
  fi
}

http_request() {
  # usage: http_request METHOD URL DATA COOKIE_FILE OUT_PREFIX [REFERER]
  local method="$1"
  local url="$2"
  local data="$3"
  local cookie="$4"
  local out_prefix="$5"
  local referer="${6:-}"

  local body_file="$TMP_DIR/${out_prefix}.body"
  local header_file="$TMP_DIR/${out_prefix}.headers"

  local curl_args=( -sS -D "$header_file" -o "$body_file" -c "$cookie" -b "$cookie" -X "$method" "$url" )

  if [[ -n "$referer" ]]; then
    curl_args+=( -e "$referer" )
  fi
  if [[ -n "$data" ]]; then
    curl_args+=( -H "Content-Type: application/x-www-form-urlencoded" --data "$data" )
  fi

  local code
  code="$(curl "${curl_args[@]}" -w '%{http_code}')"
  echo "$code"
}

extract_redirect_location() {
  local header_file="$1"
  awk 'BEGIN{IGNORECASE=1} /^Location:/{print $2; exit}' "$header_file" | tr -d '\r'
}

seed_users() {
  log "Seeding deterministic test users..."
  mysql_exec "DELETE FROM users WHERE email IN ('$ADMIN_EMAIL','$FACULTY_EMAIL','$STUDENT_EMAIL','$TEST_USER_EMAIL');" >/dev/null
  mysql_exec "INSERT INTO users (name,email,password,role,year,section,subject) VALUES
    ('$ADMIN_NAME','$ADMIN_EMAIL','$ADMIN_PASS','admin',NULL,NULL,NULL),
    ('$FACULTY_NAME','$FACULTY_EMAIL','$FACULTY_PASS','faculty',NULL,NULL,'Computer Networks'),
    ('$STUDENT_NAME','$STUDENT_EMAIL','$STUDENT_PASS','student','E2','A',NULL),
    ('$TEST_USER_NAME','$TEST_USER_EMAIL','$TEST_USER_PASS','student','E1','B',NULL);" >/dev/null

  # Remove previous test artifacts
  mysql_exec "DELETE FROM bookings WHERE subject='$SUBJECT_NAME';" >/dev/null
  mysql_exec "DELETE FROM announcements WHERE title='$ANN_TITLE';" >/dev/null
  mysql_exec "DELETE FROM courses WHERE course_code='$TEST_COURSE_CODE';" >/dev/null
  mysql_exec "DELETE FROM classrooms WHERE room_number='$TEST_ROOM';" >/dev/null
}

login_and_assert() {
  local role="$1"
  local identifier="$2"
  local password="$3"
  local cookie_file="$4"

  local code
  code="$(http_request POST "$APP_URL/login.php" "identifier=$identifier&password=$password" "$cookie_file" "login_${role}")"

  if [[ "$code" == "302" ]]; then
    local location
    location="$(extract_redirect_location "$TMP_DIR/login_${role}.headers")"
    if [[ "$role" == "admin" && "$location" == "admin_dashboard.php" ]]; then
      pass "Login redirect for admin"
    elif [[ "$role" == "faculty" && "$location" == "faculty_dashboard.php" ]]; then
      pass "Login redirect for faculty"
    elif [[ "$role" == "student" && "$location" == "student_dashboard.php" ]]; then
      pass "Login redirect for student"
    else
      fail "Login redirect for $role (code=$code, location='${location:-<none>}')"
    fi
  else
    fail "Login HTTP code for $role (expected 302, got $code)"
  fi
}

log "Starting containers..."
docker-compose up -d --build >/dev/null

DB_CID="$(docker-compose ps -q "$DB_SERVICE")"
WEB_CID="$(docker-compose ps -q "$WEB_SERVICE")"

if [[ -z "${DB_CID}" || -z "${WEB_CID}" ]]; then
  echo "[full-test] Failed to get container IDs for $DB_SERVICE/$WEB_SERVICE"
  exit 1
fi

log "Waiting for DB health..."
HEALTH=""
for _ in {1..60}; do
  HEALTH="$(docker inspect --format '{{.State.Health.Status}}' "$DB_CID" 2>/dev/null || true)"
  [[ "$HEALTH" == "healthy" ]] && break
  sleep 2
done
if [[ "$HEALTH" != "healthy" ]]; then
  echo "[full-test] DB not healthy. Recent logs:"
  docker-compose logs --tail=120 "$DB_SERVICE" || true
  exit 1
fi
pass "Database container healthy"

log "Applying schema.sql..."
docker-compose exec -T "$DB_SERVICE" mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < schema.sql
pass "Schema applied"

seed_users
pass "Seeded users and cleaned test artifacts"

log "Waiting for app HTTP readiness..."
ROOT_CODE=""
for _ in {1..40}; do
  ROOT_CODE="$(curl -s -o "$TMP_DIR/root.body" -w '%{http_code}' "$APP_URL/" || true)"
  [[ "$ROOT_CODE" == "200" ]] && break
  sleep 2
done
if [[ "$ROOT_CODE" == "200" ]]; then
  pass "Home page reachable"
  assert_contains "Home page has title" "$TMP_DIR/root.body" "BookMyClass"
else
  fail "Home page reachable (last code=${ROOT_CODE:-none})"
fi

ADMIN_COOKIE="$TMP_DIR/admin.cookie"
FACULTY_COOKIE="$TMP_DIR/faculty.cookie"
STUDENT_COOKIE="$TMP_DIR/student.cookie"

login_and_assert admin "$ADMIN_NAME" "$ADMIN_PASS" "$ADMIN_COOKIE"
login_and_assert faculty "$FACULTY_NAME" "$FACULTY_PASS" "$FACULTY_COOKIE"
login_and_assert student "$STUDENT_NAME" "$STUDENT_PASS" "$STUDENT_COOKIE"

# Admin dashboard
CODE="$(http_request GET "$APP_URL/admin_dashboard.php" "" "$ADMIN_COOKIE" "admin_dashboard")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin dashboard loads"
  assert_contains "Admin dashboard content" "$TMP_DIR/admin_dashboard.body" "Admin Dashboard"
else
  fail "Admin dashboard loads (HTTP $CODE)"
fi

# Add course
CODE="$(http_request POST "$APP_URL/manage_courses.php" "course_name=$TEST_COURSE_NAME&course_code=$TEST_COURSE_CODE" "$ADMIN_COOKIE" "admin_add_course")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin add course request returns 200"
else
  fail "Admin add course request (HTTP $CODE)"
fi
assert_db_equals "Course inserted" "SELECT COUNT(*) FROM courses WHERE course_code='$TEST_COURSE_CODE';" "1"

# Add classroom
CODE="$(http_request POST "$APP_URL/manage_classrooms.php" "room_number=$TEST_ROOM&capacity=55&type=Lecture+Hall" "$ADMIN_COOKIE" "admin_add_room")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin add classroom request returns 200"
else
  fail "Admin add classroom request (HTTP $CODE)"
fi
assert_db_equals "Classroom inserted" "SELECT COUNT(*) FROM classrooms WHERE room_number='$TEST_ROOM';" "1"

# View bookings (admin panel crash guard)
CODE="$(http_request GET "$APP_URL/view_bookings.php" "" "$ADMIN_COOKIE" "admin_view_bookings")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin bookings page loads"
  assert_contains "Bookings page has heading" "$TMP_DIR/admin_view_bookings.body" "Manage All Bookings"
  assert_not_contains "Bookings page has no fatal errors" "$TMP_DIR/admin_view_bookings.body" "Fatal error"
else
  fail "Admin bookings page loads (HTTP $CODE)"
fi

# Manage users page
CODE="$(http_request GET "$APP_URL/manage_users.php" "" "$ADMIN_COOKIE" "admin_manage_users")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin users page loads"
  assert_contains "Users page has heading" "$TMP_DIR/admin_manage_users.body" "Manage Users"
  assert_not_contains "Users page has no fatal errors" "$TMP_DIR/admin_manage_users.body" "Fatal error"
  assert_contains "Users list includes seeded admin" "$TMP_DIR/admin_manage_users.body" "$ADMIN_NAME"
else
  fail "Admin users page loads (HTTP $CODE)"
fi

# Faculty post announcement
ANN_DATA="title=$ANN_TITLE&message=$ANN_MSG&description=$ANN_MSG&year=E2&section=A&type=general"
CODE="$(http_request POST "$APP_URL/post_announcement.php" "$ANN_DATA" "$FACULTY_COOKIE" "faculty_announcement")"
if [[ "$CODE" == "200" ]]; then
  pass "Faculty post announcement request returns 200"
else
  fail "Faculty post announcement request (HTTP $CODE)"
fi
assert_db_equals "Announcement inserted" "SELECT COUNT(*) FROM announcements WHERE title='$ANN_TITLE';" "1"

# Student check availability
CODE="$(http_request POST "$APP_URL/check_availability.php" "date=$BOOK_DATE&start_time=$BOOK_START&end_time=$BOOK_END" "$STUDENT_COOKIE" "student_availability")"
if [[ "$CODE" == "200" ]]; then
  pass "Student availability request returns 200"
  assert_contains "Availability section rendered" "$TMP_DIR/student_availability.body" "Available Rooms"
else
  fail "Student availability request (HTTP $CODE)"
fi

# Student create booking
BOOK_DATA="classroom=$TEST_ROOM&subject=$SUBJECT_NAME&faculty=$FACULTY_NAME&date=$BOOK_DATE&time_from=$BOOK_START&time_to=$BOOK_END"
CODE="$(http_request POST "$APP_URL/book_class.php" "$BOOK_DATA" "$STUDENT_COOKIE" "student_booking_create")"
if [[ "$CODE" == "200" ]]; then
  pass "Student booking request returns 200"
  assert_contains "Booking success message" "$TMP_DIR/student_booking_create.body" "Booking successful"
else
  fail "Student booking request (HTTP $CODE)"
fi
assert_db_equals "Booking created" "SELECT COUNT(*) FROM bookings WHERE subject='$SUBJECT_NAME' AND room_number='$TEST_ROOM';" "1"

BOOKING_ID="$(mysql_exec "SELECT id FROM bookings WHERE subject='$SUBJECT_NAME' AND room_number='$TEST_ROOM' ORDER BY id DESC LIMIT 1;" | tr -d '\r')"
if [[ -n "$BOOKING_ID" ]]; then
  pass "Booking ID captured ($BOOKING_ID)"
else
  fail "Booking ID captured"
fi

# Student cancel booking
if [[ -n "$BOOKING_ID" ]]; then
  CODE="$(http_request POST "$APP_URL/cancel_booking.php" "booking_id=$BOOKING_ID" "$STUDENT_COOKIE" "student_booking_cancel" "$APP_URL/my_bookings.php")"
  if [[ "$CODE" == "302" ]]; then
    pass "Student cancel booking redirects"
  else
    fail "Student cancel booking redirect (HTTP $CODE)"
  fi
  assert_db_equals "Booking status cancelled" "SELECT status FROM bookings WHERE id=$BOOKING_ID;" "cancelled"
fi

# System logs page
CODE="$(http_request GET "$APP_URL/system_logs.php" "" "$ADMIN_COOKIE" "admin_system_logs")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin system logs page loads"
  assert_contains "System logs heading exists" "$TMP_DIR/admin_system_logs.body" "System Activity Logs"
  assert_not_contains "System logs page has no fatal errors" "$TMP_DIR/admin_system_logs.body" "Fatal error"
else
  fail "Admin system logs page loads (HTTP $CODE)"
fi

# Admin export report
CODE="$(http_request POST "$APP_URL/export_reports.php" "export_bookings=1" "$ADMIN_COOKIE" "admin_export")"
if [[ "$CODE" == "200" ]]; then
  pass "Admin export report request returns 200"
  assert_contains "CSV header exists" "$TMP_DIR/admin_export.body" "ID,Room,Date,Start,End,\"User ID\",Purpose,Status"
  if grep -qi "Warning:" "$TMP_DIR/admin_export.body"; then
    fail "Export CSV is clean (no PHP warnings)"
  else
    pass "Export CSV is clean (no PHP warnings)"
  fi
else
  fail "Admin export report request (HTTP $CODE)"
fi

# Admin delete user flow
DELETE_USER_ID="$(mysql_exec "SELECT id FROM users WHERE email='$TEST_USER_EMAIL' LIMIT 1;" | tr -d '\r')"
if [[ -n "$DELETE_USER_ID" ]]; then
  CODE="$(http_request GET "$APP_URL/manage_users.php?delete_id=$DELETE_USER_ID" "" "$ADMIN_COOKIE" "admin_delete_user")"
  if [[ "$CODE" == "302" || "$CODE" == "200" ]]; then
    pass "Admin delete user request processed"
  else
    fail "Admin delete user request (HTTP $CODE)"
  fi
  assert_db_equals "User deleted" "SELECT COUNT(*) FROM users WHERE id=$DELETE_USER_ID;" "0"
else
  fail "Delete user setup (test user id not found)"
fi

echo
log "Test summary: PASS=$PASS_COUNT FAIL=$FAIL_COUNT"
if [[ "$FAIL_COUNT" -gt 0 ]]; then
  echo "[full-test] FAILURES DETECTED"
  exit 1
fi

echo "[full-test] ALL TESTS PASSED"
exit 0
