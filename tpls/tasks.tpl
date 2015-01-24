<h1>Tasks - Overview</h1>

<p class="box">
    <a href="#" class="btn task-new"><span>New Task</span></a>
    <a href="#" class="btn tasks-refresh"><span>Refresh</span></a>
</p>
<div class="cleaner"></div>

<div id="tasks-table-container">
    <div id="tasks-table-overlay"></div>
    <div id="tasks-table"></div>
</div>

<p class="box">
    <a href="#" class="btn task-new"><span>New Task</span></a>
    <a href="#" class="btn tasks-refresh" data-page="1"><span>Refresh</span></a>
</p>

<div id="dialog-notification"></div>

<div id="form-new-task" data-width="400" data-height="200" class="displaynone dialog">
    <form action="" method="" class="ptx-dialog-form" id="form-new-task">
        <fieldset>
            <div class="form-group">
                <label for="TaskName">Task Name</label>
                <input type="text" class="long" id="TaskName" name="TaskName" />
                <div class="input-error"></div>
            </div>

            <div class="form-group">
                <label for="TaskContent">Task Description</label>
                <textarea class="long" id="TaskContent" name="TaskContent" cols="5" rows="3"></textarea>
                <div class="input-error"></div>
            </div>
        </fieldset>

        <div class="t-center">
            <input type="hidden" id="TaskId" value="" />
            <a href="#" class="dialog-close">close</a>
            <input type="submit" value="SUBMIT" id="new-task-submit" />
        </div>
    </form>

    <div class="errors"></div>
</div>

<div id="task-close-container" data-width="400" data-height="200" class="displaynone dialog">
    <p>You are about to close Task <strong id="task-close-name"></strong>. Are you sure you want to do this?</p>

    <div class="t-center">        
        <a href="#" class="dialog-close">close</a>
        <a href="#" id="task-close-confirm">CLOSE TASK</a>
    </div>

    <div class="errors"></div>
</div>

<div id="task-open-container" data-width="400" data-height="200" class="displaynone dialog">
    <p>You are about to open Task <strong id="task-open-name"></strong>. Are you sure you want to do this?</p>

    <div class="t-center">        
        <a href="#" class="dialog-close">close</a>
        <a href="#" id="task-open-confirm">OPEN TASK</a>
    </div>

    <div class="errors"></div>
</div>

<div id="form-report-container" data-width="400" data-height="200" class="displaynone dialog">
    <form action="" method="" class="ptx-dialog-form" id="form-report">
        <fieldset>
            <div class="form-group">
                <label for="TaskReportName">Task Name</label>
                <input type="text" class="long readonly" id="TaskReportName" name="TaskReportName" readonly="readonly" />
            </div>

            <div class="form-group">
                <label for="TaskReportTime">Time Spent</label>
                <input type="text" class="long" id="TaskReportTime" name="TaskReportTime" />
                <div class="input-help">Enter time spent in hours. e.g. 1.5 for 90 min</div>
                <div class="input-error"></div>
            </div>
        </fieldset>

        <div class="t-center">
            <input type="hidden" id="TaskReportId" value="" />
            <a href="#" class="dialog-close">close</a>
            <input type="submit" value="SUBMIT" id="task-report-submit" />
        </div>
    </form>

    <div class="errors"></div>
</div>

