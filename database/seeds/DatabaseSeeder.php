<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserRoleSeeder::class);

        $this->call(SuperAdminRolePermissionSeeder::class);
        $this->call(AdminRolePermissionSeeder::class);
        $this->call(GuestRolePermissionSeeder::class);

        $this->call(CountriesSeeder::class);   
        $this->call(CitiesSeeder::class);   
        $this->call(CompaniesSeeder::class);
        $this->call(TypeQuestionsSeeder::class);     

        $this->call(BusinessMarketRole::class);
        $this->call(role_business_market::class); 
        $this->call(Business_market_permissions::class); 
        $this->call(Permissions_business_market_user::class); 

        $this->call(Company_permissions::class); 
        $this->call(Business_market_permissions_company::class); 

        $this->call(BusinessMarketUser::class); 

        $this->call(MeetingPermisions::class); 
        $this->call(BusinessMarketMeetingPermisions::class);

        $this->call(ParticipantsSchedulePermisions::class); 
        $this->call(ParticipantsScheduleAsignToBusinessUser::class);
        $this->call(HallPermissionSeeder::class); 
        $this->call(HallAssignPermissionsSeeder::class); 

        $this->call(BM_rel_users_permisions::class); 
        $this->call(BusinessMarketMeetingPermisions::class);
        $this->call(HabeasDataPermissionsSeeder::class); 
        $this->call(HabeasDataAssingPermissionsSeeder::class); 

        $this->call(RegisterEventPermissionsSeeder::class); 
        $this->call(RegisterEventAssignPermissionsSeeder::class); 
        
        $this->call(TicketPermissionsSeeder::class);
        $this->call(TicketAssignPermissionsSeeder::class);

        $this->call(DataRegisterPermissionsSeeder::class);
        $this->call(DataRegisterAssignPermissionsSeeder::class);

        $this->call(ActivityChatPermissionsSeeder::class);
        $this->call(ActivityChatAssignPermissionsSeeder::class);
        $this->call(MeetChatPermissionsSeeder::class);

        $this->call(ProbePermisions::class);
        $this->call(ProbePermisionsAsign::class);

        $this->call(FeedbackPermissions::class);
        $this->call(FeedbackPermissionsAsign::class);

        $this->call(BMRFieldsPermissions::class);
        $this->call(BMRFieldsPermissionsAsign::class);

        $this->call(FeedbackQuestionPermisions::class);
        $this->call(FeedbackQuestionPermisionsAsign::class);
        
        $this->call(NetworkingPermissionsSeed::class);

        $this->call(StickerPermissions::class);
        $this->call(StickerPermissionsAsign::class);
        $this->call(StaffAccessPermissionsSeed::class);
        //$this->call(StaffPermissionsSeed::class);
        $this->call(LoginTrakingPermissionsSeed::class);
        $this->call(EventStylePermissionsSeed::class);
        $this->call(FontPermissionsSeed::class);
        $this->call(StaffStickerPermissionsSeeder::class);
        //
        $this->call(EventTypesSeeder::class);
    }
}
