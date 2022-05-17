
<div class="col-xs-12 no-padding-left" id="payment_slip_data" style="margin:30px; margin-top: 50px; line-height: 2.5;">
    <div class="tinyMceLetter" name="job_application" id="job_application" >
        <br>
        <br>
        <br>
        <table border="0" style="width: 100%;">

            <tr>
                <td style="width: 20%;"><b>Issue Date</b> </td>
                <td><b>: {{ date('d-F-Y') }}</b> </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td style="width: 20%;">Employee ID</td>
                <td>: <span id="letter_id"></span></td>
            </tr>
            <tr>
                <td style="width: 20%;">Employee Name</td>
                <td>: <span id="letter_name"></span></td>
            </tr>
            <tr>
                <td style="width: 20%;">Designation</td>
                <td>: <span id="letter_designation"></span></td>
            </tr>
            
            <tr>
                <td style="width: 20%;">Department</td>
                <td>: <span id="letter_department"></span></td>
            </tr>
            <tr>
                <td style="width: 20%;">Date of Join</td>
                <td>: <span id="letter_doj"></span></td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            <tr>
                <td style="width: 20%;"><strong>Subject</strong></td>
                <td>: <strong>Salary Enhancement <span id="pro_head"></span> (<span id="inType"></span>). </strong></td>
            </tr>
        </table>
        
        <p id="dear_name" style="text-transform:capitalize; margin-top:8px"> </p>

        <p style="">We are pleased to inform you that, management appreciates your efforts towards organization. <br><br>
        We are glad by awarding you a salary increase of <strong id="increment_amount" style="text-transform:capitalize"></strong> <strong id="pro_designation"></strong>. Your present Gross salary will be <strong id="grand_salary"></strong>/- with effective from <strong id="effective_date"></strong>. The upgraded salary breakdown will as below: </p>

        <br>
        <div class="salary-break-down" style="margin-left: 20%;">
            <div class="salary-head">
                <p><b>Salary Break Down:</b></p>
            </div>
            <table border="0" >
                <tr>
                    <td style="width: 40%;">Basic </td>
                    <td><p style="display:flex; justify-content:space-between;width: 60px;">: <span id="basic" class="text-right"></span> </p></td>
                </tr>
                <tr>
                    <td style="width: 40%;">House Rent </td>
                    <td><p style="display:flex; justify-content:space-between;width: 60px;">: <span id="house_rent" class="text-right"></span></p></td>
                </tr>
                <tr>
                    <td style="width: 40%;">Food Allowance </td>
                    <td><p style="display:flex; justify-content:space-between;width: 60px;">: <span id="food_allowance" class="text-right"></span></p></td>
                </tr>
                <tr>
                    <td style="width: 40%;">Medical Allowance </td>
                    <td><p style="display:flex; justify-content:space-between;width: 60px;">: <span id="medical_allowance" class="text-right"></span></p></td>
                </tr>
                <tr>
                    <td style="width: 40%;">Conveyance Allowance </td>
                    <td><p style="display:flex; justify-content:space-between;width: 60px;">: <span id="conveyance_allowance" class="text-right"></span></p></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 0px;"><hr style="margin:0px;"></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>Grand Total </b></td>
                    <td><p style="display:flex; justify-content:space-between;width: 60px; font-weight:bold;">: <span id="grand_total" style=" text-align:right !important;" ></span></p></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>In word </b></td>
                    <td><p style="display:flex; justify-content:space-between;font-weight:bold;">:&nbsp; &nbsp; &nbsp;&nbsp; <span id="salary_inword" style="text-transform:capitalize"></span></p></td>
                </tr>
            </table>
        </div>

        <br>
        <p> You are entitling to have all the benefits as admissible under service rules & regulation of the company. </p>
        <br>
        <p>We wish you a prosperous career with the company & trust that you will continue to work hard to improve your efficiency in your assignment. <b>Congratulation</b> & keep the spirit up.</p>
        
        <br>
        <br>
        <p>Sincerely Yours,</p>

        <br>
        <br>
        ------------------------------------
        
        <p style="margin-top:5px;">
        General Manager - HR
        </p>
        <p style="margin-top:10px;"> MBM Group </p>
        <br>
        <br>
        <p style="margin-bottom:5px">Copy: Personal file, Finance & Accounts. </p>

        <small>“This Letter is highly confidential & sharing the information with others will be considered as Misconduct.”</small>
    </div>
</div>