<?php include('header.php'); ?>
<div class="admin-reports">
    <h2>View Reports</h2>
    
    <div class="report-options">
        <label for="report-type">Choose Report Type:</label>
        <select id="report-type" name="report_type">
            <option value="appointments">Appointment Report</option>
            <option value="financial">Financial Report</option>
            <option value="staff">Staff Performance</option>
            <option value="inventory">Inventory Report</option>
            <option value="clients">Client Demographics</option>
            <option value="health">Pet Health Trends</option>
        </select>
    </div>

    <div class="report-details">
        <!-- Appointment Report -->
        <div id="appointments-report">
            <h3>Total Appointments</h3>
            <p><?php echo $total_appointments; ?> appointments have been scheduled</p>

            <h4>Appointment Status</h4>
            <div class="appointment-status">
                <div class="status-pending">Pending: <?php echo $pending_appointments; ?></div>
                <div class="status-confirmed">Confirmed: <?php echo $confirmed_appointments; ?></div>
                <div class="status-completed">Completed: <?php echo $completed_appointments; ?></div>
            </div>

            <h4>Appointments by Month</h4>
            <!-- Insert bar chart or table displaying appointments by month -->
        </div>

        <!-- Financial Report -->
        <div id="financial-report">
            <h3>Total Revenue</h3>
            <p><?php echo $total_revenue; ?> generated this month</p>

            <h4>Revenue Breakdown</h4>
            <!-- Insert revenue breakdown by service -->
        </div>

        <!-- Staff Performance Report -->
        <div id="staff-report">
            <h3>Doctor Appointment Load</h3>
            <!-- List of doctors with number of appointments -->
            <ul>
                <?php foreach ($doctors as $doctor) : ?>
                    <li><?php echo $doctor['name']; ?>: <?php echo $doctor['appointments_count']; ?> appointments</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Inventory Report -->
        <div id="inventory-report">
            <h3>Current Stock Levels</h3>
            <!-- List of inventory with current stock -->
        </div>

        <!-- Client Demographics -->
        <div id="clients-report">
            <h3>Client Demographics</h3>
            <!-- Display client demographics information -->
        </div>
    </div>
</div>
