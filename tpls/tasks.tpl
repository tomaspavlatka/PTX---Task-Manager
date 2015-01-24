<h1>Tasks - Overview</h1>

<p class="box">
    <a href="#" class="btn new-task"><span>New Task</span></a>
</p>
<div class="cleaner"></div>

<div id="tasks-table-container">
    <div id="tasks-table-overlay"></div>
    <div id="tasks-table"></div>
</div>

<div id="action-window"></div>
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
            <input type="submit" value="SUBMIT" id="new-task-submit" />
        </div>
    </form>
</div>