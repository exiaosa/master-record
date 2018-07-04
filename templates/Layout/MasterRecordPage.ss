<section class="MasterRecordPage">
    <div class="master-record-container">
        <% if $IsSuccess %>
            <div class="success-content">
                $getMasterRecordConfig.SubmitSuccessMessage
            </div>

        <% else_if $IsRecord %>
            <% with $getUserInfo %>
                <div class="record-container"  data-email=$Email>
                    <% if $ShowRecord %>
                        <div class="half-section right-section">
                            $Description
                        </div>
                        <div class="half-section left-section">
                            <table>
                                <tr>
                                    <th>Origin</th>
                                    <th>Date</th>
                                </tr>
                                <tbody class="data-container">
                                    <% loop $Up.getUserRecords($Email) %>
                                    <tr>
                                        <td>$RecordsClassName</td>
                                        <td>$Created</td>
                                    </tr>
                                    <% end_loop %>

                                </tbody>
                                <tr class="pagination">
                                    <td colspan="2">
                                        <% if $Up.getUserRecords($Email).MoreThanOnePage %>
                                            <% if $Up.getUserRecords($Email).NotFirstPage %>
                                                <a class="prev" href="$Up.getUserRecords($Email).PrevLink">Prev</a>
                                            <% end_if %>
                                            <% loop $Up.getUserRecords($Email).Pages %>
                                                <% if $CurrentBool %>
                                                    $PageNum
                                                <% else %>
                                                    <% if $Link %>
                                                        <a href="$Link">$PageNum</a>
                                                    <% else %>
                                                        ...
                                                    <% end_if %>
                                                <% end_if %>
                                            <% end_loop %>
                                            <% if $Up.getUserRecords($Email).NotLastPage %>
                                                <a class="next" href="$Up.getUserRecords($Email).NextLink">Next</a>
                                            <% end_if %>
                                        <% end_if %>
                                    </td>
                                </tr>

                            </table>
                            <div class="record-actions">
                                <div class="option">
                                    <span><img src="master-record/images/info.png" ></span>Do you want to download your personal file?
                                    <span class="action file-action">
                                                <a href="assets/UserData/{$getUserInfo.Email}-info.html" download="">Download</a>
                                            </span>
                                </div>
                                <div class="option">
                                    <span><img src="master-record/images/info.png" ></span>Do you want to delete your personal file?  <span class="action delete-action">Delete</span>
                                    <div class="note">$getMasterRecordConfig.DeleteNote</div>
                                </div>
                            </div>
                        </div>


                    <% else %>
                        $Error
                    <% end_if %>
                </div>
            <% end_with %>
        <% else %>
            <div class="form-container">
                $Content
                $UserRecordSubmitForm
            </div>
        <% end_if %>
    </div>
</section>