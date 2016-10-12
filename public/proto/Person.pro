syntax="proto2";  
package Proto2.Tutorial;  
message Person  
{  
    required string name = 1;  
    required int32 id = 2;  
    required string email = 3;  
  
    enum PhoneType  
    {  
        MOBILE = 0;  
        HOME = 1;  
        WORK = 2;  
    }  
  
    message PhoneNumber  
    {  
        required string number = 1;  
        required PhoneType type = 2;   
    }  
  
    repeated PhoneNumber phone = 4;  
}  
  
message AddressBook  
{  
    repeated Person person =1;  
}  
