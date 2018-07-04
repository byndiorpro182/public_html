/**

*/

// Các thư viện
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <ESP8266WebServer.h>
#include <BlynkSimpleEsp8266.h>
#include <EEPROM.h> 
#include <ESP8266HTTPClient.h>
#include <ESP8266httpUpdate.h>
#include <IRremoteESP8266.h>


// Cài đặt ban đầu

#define DEBUG_SERIAL

#define DIGITI_VERSION (20180302)


#define HAN_UPDATE         (1)      // update pin
#define HAN_WIFI_STATUS     (2)
#define HAN_PROJECT       (3)
#define HAN_DISCONNECT      (4)
#define HAN_BLYNK_CONFIG    (5)
#define HAN_BLYNK_DISCONNECT  (6)
#define HAN_CONNECT       (7)
#define HAN_DATA_WRITE      (8)
#define HAN_DATA_READ       (9)
#define HAN_UPDATE_SKETCH     (10)
#define HAN_SETUP               (11)
#define HAN_SERVER_HANDLE       (12)
#define HAN_DATA                (13)
#define HAN_SCAN_NETWORKS       (14)
#define HAN_PING        (15)

//Đăng ký Port
ESP8266WebServer server(80);


void setup (void)
{
  #ifdef DEBUG_SERIAL
    Serial.begin(115200);
    while (!Serial){}
    Serial.printf("\n[%d]Serial open\n========START SETUP==========", millis());
    Serial.printf("\n[%d] VERSION : %d", millis(), DIGITI_VERSION);
    #endif
    // Khởi tạo các pin điều khiển ở dạng OUTPUT
    for ( int i = 0; i < 17; i++)
    {
        if(i==1||i==3||i==6||i==7||i==8||i==9||i==10||i==11)
        {
            continue;
        }
        pinMode(i, OUTPUT);
    }
    // Khởi tạo bộ nhớ 1024 byte
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] EEPROM open size : 1024", millis());
    #endif
    EEPROM.begin(1024);
    
    // Cấu hình mạng WiFi AP
    wifi_config_ap();
    
    // Cấu hình mạng WiFi STA
    wifi_config_sta(export_data(14),export_data(15));
    
    // Kiểm tra server
    server_handle();
    
    // Khởi tạo server
    server.on("/", [](){server.send(200, "text/plain", "HELLO, Im'BINARY182");});
    server.on("/handle", http_handle);
    server.on("/admin",http_admin);
    server.onNotFound(handleNotFound);
    server.begin();
    
    // Cấu hình Blynk
    blynk_config();

    // Kết thúc cài đặt
    for ( int i = 0; i < 10; i++)
    {
       if(digitalRead(2))
       {
           digitalWrite(2,0); 
       }else
       {
            digitalWrite(2,1);
       }
       delay(500);
    }
    digitalWrite(2,0);  
    #ifdef DEBUG_SERIAL
    Serial.print("\n========FINISH SETUP==========");
    #endif
}
/*

*/


void loop(void)
{
  server.handleClient();   // check tín hiệu từ client
  if (sbr()) Blynk.run();     // check tín hiệu từ blynk
  check_server();           // check update từ server
}

void connect_ai(void)
{
  if(status_wifi()){return;}
    if(reset_wifi()){return;}  
    if(change_wifi()){return;}
}
bool status_wifi(void)
{
    for ( int i = 1; i < 50; i++)
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] CHECK STATUS WIFI %d : ", millis(), i);
        #endif
        if (WiFi.status() == WL_CONNECTED)
        {
            #ifdef DEBUG_SERIAL
            Serial.print("TRUE"); 
            #endif
            if(Blynk.connected())
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] CHECK BLYNK = TRUE", millis());
                #endif
                return true;
            }
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d] CHECK BLYNK = FALSE", millis());
            #endif
        }
        #ifdef DEBUG_SERIAL
        else
        {
            Serial.print("FALSE");   
        }
        #endif
        delay(100);
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] CHECK STATUS WIFI = FALSE", millis());
    #endif
    return false;
}
bool reset_wifi(void)
{
  if ( WiFi.status() == WL_CONNECTED )
  {
    save_wifi(WiFi.SSID(),WiFi.psk());
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] RESET WIFI :  ", millis());
        #endif
        WiFi.disconnect(true); delay(1000);
        if(wifi_config_sta(export_data(14),export_data(15)) && blynk_config())
        {
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d] RESET WIFI :  TRUE", millis());
            #endif
            return true;
        }
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] RESET WIFI :  FALSE", millis());
        #endif
        return false;
  }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] RESET WIFI :  FALSE", millis());
    Serial.print("\n=====");
    #endif
  return false;
}

bool change_wifi(void)
{
    int total_wifi = WiFi.scanNetworks();  delay(100);
    if ((int)EEPROM.read(21) >= 1 && (int)EEPROM.read(21) <= 5 ){}
    else
    {
        EEPROM.write(21,(uint8_t)1);
        EEPROM.commit(); delay(10);
    }

    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] total_wifi = %d", millis(),total_wifi);
    Serial.printf("\n[%d] EEPROM.read(21) = %d", millis(),(int)EEPROM.read(21));
    #endif

    for ( int i = 0; i < 5; i++ )
    {   
        if ((int)EEPROM.read(21) - i > 0)
        {
            if (export_data((int)EEPROM.read(21) - i) == "")
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] SSID%d : NULL",millis(),((int)EEPROM.read(21) - i));
                #endif
                continue;
            }           
            for ( int n = 0; n < total_wifi; ++n)
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] SSID%d : %s = %s => ",millis(),((int)EEPROM.read(21) - i), export_data((int)EEPROM.read(21) - i).c_str(),WiFi.SSID(n).c_str());
                #endif
                if (export_data((int)EEPROM.read(21) - i) == WiFi.SSID(n) )
                {
                    if(wifi_config_sta(export_data((int)EEPROM.read(21) - i),export_data((int)EEPROM.read(21) - i + 5)))
                    {
                        if(blynk_config())
                        {
                            #ifdef DEBUG_SERIAL
                            Serial.printf("\n[%d] CHANGE_WIFI :  TRUE", millis());
                            #endif
                            return true;
                        }

                    }
                }
                #ifdef DEBUG_SERIAL
                Serial.print("FALSE");
                #endif
            }
        }
        else
        {
            if (export_data(5 - (i - (int)EEPROM.read(21))) == "")
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] SSID%d : NULL",millis(),5 - ( i - (int)EEPROM.read(21)));
                #endif
                continue;
            }
            for ( int n = 0; n < total_wifi; ++n)
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] SSID%d : %s = %s => ",millis(),5 - ( i - (int)EEPROM.read(21)), export_data(5 - ( i - (int)EEPROM.read(21))).c_str(),WiFi.SSID(n).c_str());
                #endif
                if (export_data( 5 - ( i - (int)EEPROM.read(21))) == WiFi.SSID(n))
                {
                    if(wifi_config_sta(export_data(5 - ( i - (int)EEPROM.read(21))),export_data(5 - ( i - (int)EEPROM.read(21)) + 5)))
                    {
                        if(blynk_config())
                        {
                            #ifdef DEBUG_SERIAL
                            Serial.printf("\n[%d] CHANGE_WIFI :  TRUE", millis());
                            #endif
                            return true;
                        }

                    }
                }
                #ifdef DEBUG_SERIAL
                Serial.print("FALSE");
                #endif
            }
        }
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] CHANGE_WIFI :  FALSE", millis());
    #endif
    return false;
}




/*
ssid1 = 1;
ssid2 = 2;
ssid3 = 3;
ssid4 = 4;
ssid5 = 5;
pass1 = 6;
pass2 = 7;
pass3 = 8;
pass4 = 9;
pass5 = 10;
auth = 11;
ap_ssid = 12;
ap_pass = 13;
ssid_now = 14;
pass_now = 15;
local_ip = 16;
getway = 17;
subnet = 18;
 = 19;
 = 20;
ota_update = 21;

nhập xuất địa chỉ ip
 String abc = "";
 abc += ((char)ip[0]);
 abc += ((char)ip[1]);
 abc += ((char)ip[2]);
 abc += ((char)ip[3]);
 import_data(1, abc); \\ nhập vào vị trí 1
  int location = 1;    \\ nên xuất ở vị trí 1
  int first_location = (location*40 + 1);
  for ( int i = 0; i < 40; i++)
  {
    if ( EEPROM.read( i + first_location) == 0 ) break;
  }

  cách chuyển chuỗi char sang String và const char*  ( eRead[i] chính là chuỗi char)
  const char* Name;
    tempR = "";
    for (byte i = 0; i < len; i++)
    {
      tempR += eRead[i];
    }
    Name = tempR.c_str();

function 
 import_data();
 export_data();

*/

bool import_data ( int location, String import_value )
{   
    
    if ( location < 1 || location > 25 || import_value.length() > 40)
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] LOCATION = %d AND VALUE = %s", millis(), location, import_value.c_str());
        #endif
        return false;
    }
    char value[40];
    int first_location = (location*40 + 1);
    import_value.toCharArray( value, import_value.length() + 1);
    for ( int i = 0; i < 40; i++)
    {
        EEPROM.write( i + first_location, 0);
    }
    delay(100);
    for ( int i = 0; i < strlen(value); i++)
    {
        EEPROM.write( i + first_location, (uint8_t)value[i]);
    }
    EEPROM.commit(); delay(50);
    return true;
}

String export_data ( int location)
{   
    String result = "";
    if(location < 1 || location > 25)
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] LOCATION = %d => FALSE", millis(), location);
        #endif
        return result;
    }
    int first_location = (location*40 + 1);
    for ( int i = 0; i < 40; i++)
    {
        if ( EEPROM.read( i + first_location) == 0 || EEPROM.read( i + first_location) == 255) break;
        result += (char)EEPROM.read( i + first_location);
    }
    return result;
}

void save_wifi(String sta_ssid, String sta_pass)
{
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] SET SSID_NOW = %s, PASS_NOW = %s", millis(), sta_ssid.c_str(), sta_pass.c_str());
    #endif
    if(export_data(14) == sta_ssid)
    {
        if (export_data(15) != sta_pass)
        {
            import_data(15,sta_pass);
        }
    }
    else
    {
        import_data(14,sta_ssid);
        import_data(15,sta_pass);
    }
    for ( int i = 1; i <= 5; i++)
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] SSID%d = %s = %s => ", millis(),i,export_data(i).c_str(),sta_ssid.c_str());
        #endif
        if(sta_ssid == export_data(i))
        {
            #ifdef DEBUG_SERIAL
            Serial.printf("TRUE , PASS%d = %s = %s => ", i, export_data(i+5).c_str(), sta_pass.c_str());
            #endif
            if(sta_pass == export_data(i+5))
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("TRUE => NOT SAVE");
                #endif
                return;
            }
            #ifdef DEBUG_SERIAL
            Serial.print("FALSE => SAVE");
            #endif
        }
        #ifdef DEBUG_SERIAL
        else
        {
            Serial.print("FALSE");
        }
        #endif
    }

    if( (int)EEPROM.read(21) < 5 )
    {
        EEPROM.write(21, (uint8_t)((int)EEPROM.read(21) + 1));
        EEPROM.commit(); delay(10);
        import_data((int)EEPROM.read(21), sta_ssid);
        import_data((int)EEPROM.read(21)+5, sta_pass);
    }
    else
    {
        EEPROM.write(21,(uint8_t)1);
        EEPROM.commit(); delay(10);
        import_data( 1 , sta_ssid);
        import_data( 6 , sta_pass);
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] SAVE %s => %d AND %s => %d", millis(), sta_ssid.c_str(), (int)EEPROM.read(21), sta_pass.c_str(), (int)EEPROM.read(21) + 5);
    #endif
}


bool wifi_config_ap(void)
{
    WiFi.mode(WIFI_AP_STA);
    if (EEPROM.read(641) == 255 && EEPROM.read(642) == 255 && EEPROM.read(643) == 255 && EEPROM.read(644) == 255)
    {
        EEPROM.write(641, 182);
        EEPROM.write(642, 182);
        EEPROM.write(643, 182);
        EEPROM.write(644, 182);
  
        EEPROM.write(681, 182);
        EEPROM.write(682, 182);
        EEPROM.write(683, 182);
        EEPROM.write(684, 1);
  
        EEPROM.write(721, 255);
        EEPROM.write(722, 255);
        EEPROM.write(723, 255);
        EEPROM.write(724, 0);
        EEPROM.commit(); delay(50);
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] START WIFI CONFIG AP : ", millis());
    Serial.printf("\n[%d] LOCAL IP : ", millis());
    Serial.print(EEPROM.read(641)); Serial.print(".");
    Serial.print(EEPROM.read(642)); Serial.print(".");
    Serial.print(EEPROM.read(643)); Serial.print(".");
    Serial.print(EEPROM.read(644));
    Serial.printf("\n[%d] GATEWAY : ", millis());
    Serial.print(EEPROM.read(681)); Serial.print(".");
    Serial.print(EEPROM.read(682)); Serial.print(".");
    Serial.print(EEPROM.read(683)); Serial.print(".");
    Serial.print(EEPROM.read(684));
    Serial.printf("\n[%d] SUBNET : ", millis());
    Serial.print(EEPROM.read(721)); Serial.print(".");
    Serial.print(EEPROM.read(722)); Serial.print(".");
    Serial.print(EEPROM.read(723)); Serial.print(".");
    Serial.print(EEPROM.read(724));
    #endif
    IPAddress local_ip(EEPROM.read(641),EEPROM.read(642),EEPROM.read(643),EEPROM.read(644));
    IPAddress gateway(EEPROM.read(681),EEPROM.read(682),EEPROM.read(683),EEPROM.read(684));
    IPAddress subnet(EEPROM.read(721),EEPROM.read(722),EEPROM.read(723),EEPROM.read(724));
    if (export_data(12).length() < 1 || export_data(13).length() < 8)
    {
        import_data(12, "BINARY");
        import_data(13, "12345678");
    }
    if(WiFi.softAP(export_data(12).c_str(),export_data(13).c_str()) && WiFi.softAPConfig(local_ip, gateway, subnet))
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] WiFi softAP : AP_SSID = %s , AP_PASS = %s ", millis(), export_data(12).c_str(),export_data(13).c_str());
        Serial.printf("\n[%d] END WIFI CONFIG AP", millis());
        #endif
        return true;
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] WiFi softAP : AP_SSID = %s , AP_PASS = %s ", millis(), export_data(12).c_str(),export_data(13).c_str());
    Serial.printf("\n[%d] END WIFI CONFIG AP", millis());
    #endif
    return false;
}    
    
bool wifi_config_sta(String STA_SSID, String STA_PASS)
{
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] START CONNECT :  ", millis());
    #endif
    if ( STA_SSID.length() < 1)
    {
        #ifdef DEBUG_SERIAL
        Serial.print("STA_SSID = NULL => FALSE");
        Serial.printf("\n[%d] END CONNECT.", millis());
        #endif
        return false;
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("STA_SSID : %s AND STA_PASS : %s ", STA_SSID.c_str(),STA_PASS.c_str());
    #endif
    WiFi.hostname("Binary");
    WiFi.begin(STA_SSID.c_str(),STA_PASS.c_str());
    for ( int i = 0; i < 30; i++)
    {
        if ( WiFi.status() == WL_CONNECTED )
        {
            #ifdef DEBUG_SERIAL
            Serial.print(" OK ");
            Serial.printf("\n[%d] STA_SSID : %s", millis(), WiFi.SSID().c_str());
            Serial.printf("\n[%d] STA_PASS : %s", millis(), WiFi.psk().c_str());
            Serial.printf("\n[%d] IP : ",millis());
            Serial.print(WiFi.localIP());
            Serial.printf("\n[%d] END CONNECT", millis());
            #endif
            save_wifi(WiFi.SSID(),WiFi.psk());
            blynk_config();
            return true;
        }
        #ifdef DEBUG_SERIAL
        else{Serial.print(".");}
        #endif
        delay(500);
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("FALSE");
    Serial.printf("\n[%d] END CONNECT", millis());
    #endif    
    return false;
}

bool blynk_config(void)
{
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] STAR BYNK CONFIG :  ", millis());
    #endif
    if(export_data(11).length() == 32)
    {
        String auth = export_data(11);
        Blynk.config(auth.c_str());
        #ifdef DEBUG_SERIAL
        Serial.printf("AUTH = %s",export_data(11).c_str());
        #endif
        if ( WiFi.status() == WL_CONNECTED)
        {
            if(Blynk.connect() && Blynk.connected())
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] BLYNK CONNECT : OK", millis());
                Serial.printf("\n[%d] END BLYNK CONNECT", millis());  
                #endif
                return true;
            }
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d] BLYNK CONNECT : FALSE", millis());
            Serial.printf("\n[%d] END BLYNK CONNECT", millis());
            #endif
            return false;
        }
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] WIFI NOT CONNECTED", millis());
        Serial.printf("\n[%d] END BLYNK CONNECT", millis());
        #endif
        return false;
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("AUTH = %s , LENGTH = %d",export_data(11).c_str(),export_data(11).length());
    Serial.printf("\n[%d] END BLYNK CONNECT", millis());
    #endif
    return false;
}


String solve(String _code, String _solve_code)            //String _code = "bien=abc&xyz=123&vkl=hh1"; String _solve_code = "bien";
{
    if(_code.length() < _solve_code.length())
    {
      return "";
    }    
    String code = "&" + (_code);
    String solve_code = "&" + (_solve_code);
    char char_code[code.length()];
    code.toCharArray(char_code, code.length() + 1);
    for ( int i = 0; i < code.length() - solve_code.length() + 1; i++ )
    {
        String caching = "";
        for(int n = 0; n <solve_code.length(); n++)
        {
            caching += char_code[n+i];
        }
        if (caching == solve_code)
        {
            String result = "";
            for(int z = 0; z < code.length() - i - solve_code.length(); z++)
            {
                if (char_code[i + solve_code.length() + 1 + z] == 38)
                break;
                result += char_code[i+solve_code.length() + 1 + z];
            }
            return result;
        }
    }
    return "";
}

String access(String _url)   //"/abc/xyz?d=123" co dau gach cheo dau tien // trả về nội dung trang web
{
    String result = "";
    String url = "";
    char buf[_url.length()];
    _url.toCharArray(buf, _url.length() + 1);
    for ( int i = 0; i < _url.length(); i++)
    {
        if ((uint8_t)buf[i] == 32)
        {
            url += "%20";
            continue;
        }
        url += buf[i];
    }    
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] ACCESS URL : %s", millis(), url.c_str());
    #endif
    WiFiClient client;
    const int httpPort = 80;
    const char* host = "hoangvanhieu182.000webhostapp.com";
    if (!client.connect(host, httpPort))
    {    
        result += "false";
        return result;
    }
    client.printf("GET %s HTTP/1.1\r\nHost: %s\r\nConnection: close\r\n\r\n",url.c_str(),host);
    int timeout = millis();
    while (client.available() == 0)
    {
        if (millis() - timeout > 5000) 
        {
            client.stop();
            result += "false";
            return result;
        }
    }
    if(client.available())
    {
        String line = client.readStringUntil('\R');
        line = "";
    }
    if(client.available())
    {
        String line = client.readStringUntil('\R');
        line.remove(0,49);
        line.remove(line.length() - 7);
        line.trim();
        result += line;
        return result;
    }
    return result;
}

bool sbr()
{   
    static unsigned long time_sbr = 0;
    static boolean x = true;
    static unsigned long time_bc = 0;
    #ifdef DEBUG_SERIAL
    static unsigned int time_loop = 0;
    static unsigned int buf_time_loop = 0;
    time_loop = millis() - buf_time_loop;
    buf_time_loop = (unsigned int)millis();
    #endif
    if(millis() - time_sbr > 10000)
    {
        #ifdef DEBUG_SERIAL
        if (WiFi.status() == WL_CONNECTED)
        {
            Serial.printf("\n[%d] SBR : TRUE", millis());
        }
        else
        {
            Serial.printf("\n[%d] SBR : FALSE", millis());
        }
        if (Blynk.connected())
        {
            Serial.print(" || BLYNK : TRUE");
        }
        else
        {
            Serial.print(" || BLYNK : FALSE");
        }
        Serial.printf(" || TIME LOOP : %d", time_loop);
        #endif
        if(WiFi.status() == WL_CONNECTED)
        {
            if (!Blynk.connected())
            {
                if(millis() - time_bc > 300000)
                {
                    blynk_config();
                    time_bc = millis();
                }
                x = false;
                time_sbr = millis();
                return false;
            }
            x = true;
            time_sbr = millis();
            return true;
        }
        else
        {x = false; time_sbr = millis(); return false;}     
    }
    return x;
}


BLYNK_WRITE(V1)   //V1 gửi cho 8266, V2 nhận trả lời từ 8266            // khi chân V1 có thay đổi thì hàm sẽ được chạy , sử dụng chân ảo V1 để truyền thông tin
{
    String m =  param.asStr();                //giá trị của V1
    int h = param.asInt();

    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] V1 :%s", millis(), m.c_str());
    #endif

//  Blynk.virtualWrite(V1, "abcdef");    // sử dụng chân ảo V1 để gửi thông tin lên chân V1
}








#ifndef DEBUG_SERIAL
BLYNK_CONNECTED()
{
    Blynk.syncAll();
}
#endif

/*
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <WiFiUdp.h>
#include <ESP8266HTTPClient.h>
#include <StreamString.h>

byte http_update(const String& url)
{

    HTTPClient http;
    http.begin(url);
    http.useHTTP10(true);
    http.setTimeout(8000);
    http.setUserAgent(F("ESP8266-http-Update"));
    http.addHeader(F("x-ESP8266-STA-MAC"), WiFi.macAddress());
    http.addHeader(F("x-ESP8266-AP-MAC"), WiFi.softAPmacAddress());
    http.addHeader(F("x-ESP8266-free-space"), String(ESP.getFreeSketchSpace()));
    http.addHeader(F("x-ESP8266-sketch-size"), String(ESP.getSketchSize()));
    http.addHeader(F("x-ESP8266-sketch-md5"), String(ESP.getSketchMD5()));
    http.addHeader(F("x-ESP8266-chip-size"), String(ESP.getFlashChipRealSize()));
    http.addHeader(F("x-ESP8266-sdk-version"), ESP.getSdkVersion());
    
    const char * headerkeys[] = { "x-MD5" };
    size_t headerkeyssize = sizeof(headerkeys) / sizeof(char*);
    http.collectHeaders(headerkeys, headerkeyssize);


    int code = http.GET();
    int len = http.getSize();

    if(code <= 0) 
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] HTTP error: ", millis());  
        Serial.print(http.errorToString(code).c_str());
        Serial.printf("\n[%d] code : ", millis());
        Serial.print(code);
        #endif
        http.end();
        return 0;
    }

    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] code: ", millis());
    Serial.print(code);
    Serial.printf("\n[%d] len: ", millis());
    Serial.println(len);
    #endif

    if(http.hasHeader("x-MD5"))
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] MD5: ", millis()); 
        Serial.print(http.header("x-MD5").c_str());
        #endif
    }

    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] free Space: ", millis());
    Serial.print(ESP.getFreeSketchSpace());
    Serial.printf("\n[%d] current Sketch Size: ", millis());
    Serial.println(ESP.getSketchSize());
    #endif

    switch(code) 
    {
    case HTTP_CODE_OK:
        if(len > 0)
        {
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d]Free Sketch Space = ", millis());
            Serial.print((int)ESP.getFreeSketchSpace());
            Serial.printf("\n[%d]Size file update = ", millis());
            Serial.print(len);
            #endif
            if (len > (int) ESP.getFreeSketchSpace()) 
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] Size file update too big", millis());
                #endif
                return 0;
            }
            WiFiClient * tcp = http.getStreamPtr();
            WiFiUDP::stopAll();
            WiFiClient::stopAllExcept(tcp);
            delay(100);
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d] runUpdate flash...", millis());
            #endif
            uint8_t buf[4];
            if(tcp->peekBytes(&buf[0], 4) != 4)
            {
                #ifdef DEBUG_SERIAL    
                Serial.printf("\n[%d] peekBytes magic header failed", millis());
                #endif
                http.end();
                return 0;
            }
            if(buf[0] != 0xE9)
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] magic header not starts with 0xE9", millis());
                #endif
                http.end();
                return 0;
            }
            uint32_t bin_flash_size = ESP.magicFlashChipSize((buf[3] & 0xf0) >> 4);
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d] ESP Flash Chip read size = ", millis());
            Serial.print(ESP.getFlashChipRealSize());
            #endif
            if(bin_flash_size > ESP.getFlashChipRealSize()) 
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] magic header, new bin not fits SPI Flash", millis());
                #endif
                http.end();
                return 0;
            }
            int command;
            command = U_FLASH;
            if(runUpdate(*tcp, len, http.header("x-MD5"), command))
            {
                http.end();
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] Update ok", millis());
                Serial.printf("\n[%d]Luu vao Sketch thanh cong : bat dau reset", millis());
                #endif
                ESP.restart();
            } 
            else 
            {
                #ifdef DEBUG_SERIAL
                Serial.printf("\n[%d] Update failed", millis());
                #endif
            }
        }
        else 
        {
            #ifdef DEBUG_SERIAL
            Serial.printf("\n[%d] Content-Length is 0 or not set by Server?!", millis());
            #endif
        }
        break;
    default:
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] HTTP CODE FALSE : ", millis());
        Serial.print(code);
        #endif
        break;
    }
    http.end();
    return 0;
}


bool runUpdate(Stream& in, uint32_t size, String md5, int command)
{

    StreamString error;
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] size = ", millis());
    Serial.print((int)size);
    Serial.printf("\n[%d] md5 = ", millis());
    Serial.print(md5.c_str());
    Serial.printf("\n[%d] command = ", millis());
    Serial.print(command);
    #endif

    if(!Update.begin(size, command)) 
    {
        Update.printError(error);
        error.trim();
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] UPDATE ERROR : ", millis() );
        Serial.print(Update.getError());
        Serial.printf("\n[%d] Update.begin failed : ", millis());
        Serial.print(error.c_str());
        #endif
        return false;
    }
    if(md5.length()){}
    else
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] md5 length : ", millis());
        Serial.print(md5.length());
        #endif
        return false;
    }
    if(!Update.setMD5(md5.c_str()))
    {
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] Update.setMD5 failed : ", millis());
        Serial.print(md5.c_str());
        #endif
        return false;
    }
    if(Update.writeStream(in) != size)
    {
        Update.printError(error);
        error.trim();
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] UPDATE ERROR : ", millis());
        Serial.print(Update.getError());
        Serial.printf("\n[%d] Update.writeStream failed : ", millis());
        Serial.print(error.c_str());
        #endif
        return false;
    }
    if(!Update.end())
    {
        Update.printError(error);
        error.trim();
        #ifdef DEBUG_SERIAL
        Serial.printf("\n[%d] UPDATE ERROR : ", millis());
        Serial.print(Update.getError());
        Serial.printf("\n[%d] Update.end failed : ", millis());
        Serial.print(error.c_str());
        #endif
        return false;
    }
    return true;
}
*/

String api(String url) {
    HTTPClient http;
    http.begin(url); //HTTP
    http.setTimeout(5000);
    int httpCode = http.GET();
    if(httpCode > 0) {
        String payload = http.getString();
        http.end();
        return payload;
    }
    http.end();
}

void binary_log(const char* fmt, ... ){
    char buf[256];
    va_list ap;
    va_start (ap, fmt );
    vsnprintf(buf, 256, fmt, ap);
    va_end (ap);
    String result = "";
    for (int i = 0; i < 256; i++)
    {
      result += buf[i];
    }
    Serial.println(result);
}

/*
String ir_recv(const int IR_RECV_PIN)
{
  IRRecv irRecv;

  const int MAX_IR_BUFFER_LENGTH = 800;   // size của biến lưu trữ tín hiệu
  unsigned int irBuffer[MAX_IR_BUFFER_LENGTH];  // khởi tạo biến lưu trữ
  int currentIrBufferLength = 0;

  irRecv.start(IR_RECV_PIN, irBuffer, MAX_IR_BUFFER_LENGTH, currentIrBufferLength);
  for(int i = 0; i < 30; i++)
  {
      delay(1000);
      if (currentIrBufferLength > 0) {
      irRecv.stop(IR_RECV_PIN);
      String result = String(currentIrBufferLength) + ":";
      
      for (int i = 0; i < currentIrBufferLength; i++) {
        result += String(irBuffer[i]) + ",";
      }
      return result;
    }
  }
  return "";
}

bool ir_send(String data)
{
    IRSend irSend;

    const int FREQ_KHZ = 38;
    int currentIrBufferLength = 0;
    const int MAX_IR_BUFFER_LENGTH = 800;
    unsigned int irBuffer[MAX_IR_BUFFER_LENGTH];

    if(data.indexOf(":") != -1){

        if(data.charAt(data.length()-1) != 44)
        data += ",";
        
        String dodaichuoi = "";
        String so = "";
        int buff_number = 0;
        boolean laco = false;

        for(int i = 0; i < data.length(); i++)
        {
            if(laco)
            {
                if(data.charAt(i) != 44)
                {
                    so += String(data.charAt(i));
                }else{
                    irBuffer[buff_number] = so.toInt();
                    so = "";
                    buff_number++; 
                }

            }else{
                if(data.charAt(i) != 58)
                {
                    dodaichuoi += String(data.charAt(i));
                }else{
                    currentIrBufferLength = dodaichuoi.toInt();
                    if(currentIrBufferLength > 800) return false;
                    laco = true;
                }
            }
        }
    }else{return false;}

    irSend.sendRaw(irBuffer, currentIrBufferLength, FREQ_KHZ, false);
    return true;
}
*/

 /*
    s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
    s += "<body><form action='/admin?han=2' method='POST'><br /><H1> Successfully connected </H1><br />";
    s += "============================<br />";
    s += "============================<br />";
    s += "</from></body></html>";
    server.send(200,"text/html",s);
*/

void http_admin()
{
    String s = "";
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] URI : %s", millis(), server.uri().c_str());
    Serial.printf("\n[%d] ARGUMENTS : %d ||", millis(), server.args());
    for (uint8_t i=0; i<server.args(); i++)
    {
        Serial.printf(" %s = %s ||",server.argName(i).c_str(),server.arg(i).c_str());
    }
    #endif

    int han = server.arg("han").toInt();

    switch(han)
    {
        case HAN_UPDATE:                                    //http://ip/admin?han=1&pin=2&value=1
                    {
                        if (server.arg("value") != "")
                        {
                            if(server.arg("pin")=="0"||server.arg("pin")=="2"||server.arg("pin")=="4"||server.arg("pin")=="5"||server.arg("pin")=="12"||server.arg("pin")=="13"||server.arg("pin")=="14"||server.arg("pin")=="15"||server.arg("pin")=="16")
                            {
                                digitalWrite(server.arg("pin").toInt(),server.arg("value").toInt());
                            } 
                        }
                        s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                        s += "<body><form action='/admin?han=1' method='POST'><br /><H1> Successfully connected </H1><br />";
                        s += "============================<br />";
                        for (int i = 0; i < 17; i++)
                        {
                            if(i==1||i==3||i==6||i==7||i==8||i==9||i==10||i==11)continue;
                            s += "PIN" + String(i) + " : ";
                            if (digitalRead(i))
                            {
                                s += "<a href='/admin?han=1&pin=" + String(i) + "&value=0'>ON</a><br />";
                            }
                            else
                            {
                                s += "<a href='/admin?han=1&pin=" + String(i) + "&value=1'>OFF</a><br />";
                            }
                        }
                        s += "============================<br />";
                        s += "<br /><a href='/admin'>BACK</a><br />";
                        s += "</from></body></html>";
                        server.send(200,"text/html",s);
                        break;
                    }
        case HAN_WIFI_STATUS:                               //http://ip/admin?han=2&sta_ssid=DuongBui&sta_pass=19011980
                    {
                        if (server.arg("sta_ssid") != "" && server.arg("connect") == "true")
                        {
                            s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                            s += "<body><form action='/admin?han=2' method='POST'><br /><H1> Successfully connected </H1><br />";
                            s += "============================<br />";
                            s += "CONNECT TO SSID : " + (server.arg("sta_ssid")) + " ,PASS : " + (server.arg("sta_pass"));
                            s += "BACK AFTER 15 SECOND ....";
                            s += "<br />============================<br />";
                            s += "<a href='/admin?han=2'>BACK</a></from></body></html>";
                            server.send(200,"text/html",s);
                            wifi_config_sta(server.arg("sta_ssid"),server.arg("sta_pass"));
                            break;
                        }
                        if (server.arg("disconnect") == "true")
                        {
                            s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                            s += "<body><form action='/admin?han=2' method='POST'><br /><H1> Successfully connected </H1><br />";
                            s += "============================<br />";
                            s += "DISCONNECT = TRUE <br />";
                            s += "============================<br />";
                            s += "<a href='/admin?han=2'>BACK</a></from></body></html>";
                            server.send(200,"text/html",s);
                            WiFi.disconnect(true);
                            break;
                        }
                        s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                        s += "<body><form action='/admin?han=2' method='POST'><br /><H1> Successfully connected </H1><br />";
                        s += "============================<br />";
                        s += "<H3>NETWORK CONNECTED</H3>";
                        if(WiFi.status() == WL_CONNECTED)
                        {
                            s += "STA_SSID : " + String(WiFi.SSID()) + "<br />";
                            s += "STA_PASS : " + String(WiFi.psk()) + "<br />";
                            s += "<a href='/admin?han=2&disconnect=true'>DISCONNECT</a>";
                        }
                        else
                        {
                            s += "NOT CONNECTED <br />";
                        }
                        s += "<H3>NETWORK NOW</H3>";
                        int total_wifi = WiFi.scanNetworks(); delay(20);
                        for ( int i = 0; i < total_wifi; ++i)
                        {
                            s += String(i + 1) + " : " + "<a href='/admin?han=2&sta_ssid=" + String(WiFi.SSID(i)) +"'>" + String(WiFi.SSID(i)) + "</a>";
                            if (server.arg("sta_ssid") == WiFi.SSID(i))
                            {
                                s += " || STA_PASS : <input type='text' name='sta_pass'>";
                            }
                            s += "<br />";
                        }
                        s += "============================<br />";
                        if ( server.arg("sta_ssid") != "" )
                        {
                            for (int i = 0; i < total_wifi; ++i)
                            {
                                if (server.arg("sta_ssid") == WiFi.SSID(i))
                                {
                                    s += "<button type='submit' name='connect' formaction='/admin?han=2&sta_ssid=" + String(WiFi.SSID(i)) + "' value='true'> CONNECT</button><br /><br />";  
                                }
                            }    
                        }
                        s += "<br /><a href='/admin'>BACK</a><br />";
                        s += "</from></body></html>";
                        server.send(200,"text/html",s);
                        break;
                    }
        case HAN_SERVER_HANDLE:
                    {
                        String message = "";
                        if(server.arg("update") == "true")
                        {
                            server_handle();
                            message = "VERSION NOW : " + String(DIGITI_VERSION) ;
                        }
                        else
                        {
                            message = "<button type='submit' name='update' value='true'>CHECK UPDATE</button><br />";
                        }
                        s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                        s += "<body><form action='/admin?han=12' method='POST'><br /><H1> Successfully connected </H1><br />";
                        s += "============================<br />";
                        s += message;
                        s += "<br />============================<br />";
                        s += "<br /><a href='/admin'>BACK</a><br />";
                        s += "</from></body></html>";
                        server.send(200,"text/html",s);
                    }
        case HAN_SETUP:
                    {
                        String notification = "";
                        if(server.arg("restart") == "true" && server.arg("ap_ssid") != "" || server.arg("ap_pass") != "")
                        {
                            if (server.arg("ap_ssid") != ""){
                                import_data(12,server.arg("ap_ssid"));
                            }
                            if (server.arg("ap_pass") != ""){
                                if(server.arg("ap_pass").length() > 7){
                                    import_data(13,server.arg("ap_pass"));
                                }
                            }
                            s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                            s += "<body><form action='/admin?han=2' method='POST'><br /><H1> Successfully connected </H1><br />";
                            s += "============================<br />";
                            s += "RESTART ....... TRUE <br /> pass length must be greater than 8 ";
                            s += "============================<br />";
                            s += "<a href='/admin?han=11'>BACK</a></from></body></html>";
                            server.send(200,"text/html",s);
                            delay(500);
                            ESP.restart();
                        }
                        if(server.arg("connect") == "true")
                        {
                            if (server.arg("auth").length() == 32)
                            {
                                import_data(11,server.arg("auth"));
                                notification += "<H4> SAVE AUTH ... OK </H4>";
                            }
                            if(blynk_config())
                            {
                                notification += "<H4> CONNECT ... OK </H4>";
                            }
                            else
                            {
                                notification += "<H4> CONNECT ... FALSE </H4>";
                            }
                        }
                        if(server.arg("auth") != "" && server.arg("save") == "save")
                        {
                            if (server.arg("auth").length() == 32)
                            {
                                import_data(11,server.arg("auth"));
                                notification += "<H4> SAVE AUTH ... OK </H4>";
                            }else{
                                notification += "<H4> SAVE AUTH ... FALSE </H4>"; 
                            }
                            
                        }
                        s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                        s += "<body><form action='/admin?han=11' method='POST'><br /><H1> Successfully connected </H1><br />";
                        s += "============================<br />";
                        s += (notification);
                        s += "<H1>Setup</H1>";
                        s += "AP_SSID : " + (export_data(12)) + "<br />" + "<input type='text' name='ap_ssid'><br />";
                        s += "AP_PASS : " + (export_data(13)) + "<br />" + "<input type='text' name='ap_pass'><br /><br />";
                        s += "<button type='submit' name='restart' value='true'>RESTART</button><label>     ==> Hardware to restart </label>";
                        s += "<H1> Status server </H1>";
                        if (Blynk.connected())
                        {
                            s += "<H3>SERVER CONNECTED = TRUE </H3>";
                        }
                        else
                        {
                            s += "<H3>SERVER CONNECTED = FALSE</H3>";
                        }
                        s += "<br /><br />Auth : " + (export_data(11)) + "<br />" + "<input type='text' name='auth'> ==> ";
                        s += "<button type='submit' name='save' value='save'>SAVE</button><br />";
                        s += "<br /><button type='submit' name='connect' value='true'>CONNECT</button><br />";
                        s += "============================<br />";
                        s += "<br /><a href='/admin'>BACK</a><br />";
                        s += "</from></body></html>";
                        server.send(200,"text/html",s);
                    }
        case HAN_DATA:
                    {
                        for ( int i = 1; i < 26; i++)
                        {
                            if ( i == 16 || i == 17 || i == 18)
                            {
                                String content1 = "location"; content1 += (i*40+1);
                                String content2 = "location"; content2 += (i*40+2);
                                String content3 = "location"; content3 += (i*40+3);
                                String content4 = "location"; content4 += (i*40+4);       
                                if(server.arg(content1) != "" && server.arg(content2) != "" && server.arg(content3) != "" && server.arg(content4) != "")
                                {
                                    if(server.arg(content1).toInt() >= 0 && server.arg(content2).toInt() >= 0 && server.arg(content3).toInt() >= 0 && server.arg(content4).toInt() >= 0)
                                    {
                                        if (server.arg(content1).toInt() < 256 && server.arg(content2).toInt() < 256 && server.arg(content3).toInt() < 256 && server.arg(content4).toInt() < 256)
                                        {
                                            EEPROM.write(i*40+1,server.arg(content1).toInt());
                                            EEPROM.write(i*40+2,server.arg(content2).toInt());
                                            EEPROM.write(i*40+3,server.arg(content3).toInt());
                                            EEPROM.write(i*40+4,server.arg(content4).toInt());
                                            EEPROM.commit(); delay(20);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                String content = "location"; content += (i);
                                if (server.arg(content) != "")
                                {
                                  import_data(i,server.arg(content));
                                }
                            }                     
                        }
                        s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                        s += "<body><form action='/admin?han=13' method='POST'><br /><H1> Successfully connected </H1><br />";
                        s += "============================<br />";
                        for (int i = 1; i < 26; i++ )
                        {
                            if ( i == 16 || i == 17 || i == 18)
                            {
                                if ( i == 16)
                                {
                                    s += String(i) + " : " + "IPAddress LOCAL IP : " + String(EEPROM.read(641)) + "." + String(EEPROM.read(642))  + "." + String(EEPROM.read(643))  + "." + String(EEPROM.read(644)) + "<br />";
                                    s += "<input type='text' name='location641'>.<input type='text' name='location642'>.<input type='text' name='location643'>.<input type='text' name='location644'><br />";
                                }
                                if ( i == 17)
                                {
                                    s += String(i) + " : " + "IPAddress GATEWAY : " + String(EEPROM.read(681))  + "." + String(EEPROM.read(682))  + "." + String(EEPROM.read(683))  + "." + String(EEPROM.read(684)) + "<br />";
                                    s += "<input type='text' name='location681'>.<input type='text' name='location682'>.<input type='text' name='location683'>.<input type='text' name='location684'><br />";
                                }
                                if ( i == 18)
                                {
                                    s += String(i) + " : " + "IPAddress SUBNET : " + String(EEPROM.read(721)) + "." + String(EEPROM.read(722))  + "." + String(EEPROM.read(723))  + "." + String(EEPROM.read(724)) + "<br />";
                                    s += "<input type='text' name='location721'>.<input type='text' name='location722'>.<input type='text' name='location723'>.<input type='text' name='location724'><br />";
                                } 
                            }
                            else
                            {
                                s += String(i) + " : " + "<input type='text' name='location" + String(i) + "'>" + " || " + export_data(i) + "<br />";
                            }
                        }   
                        s += "============================<br />";
                        s += "<input type='submit' name='SUBMIT' value='SEND'> || ";
                        s += "<a href='/admin'>BACK</a><br />";
                        s += "</from></body></html>";
                        server.send(200,"text/html",s);
                        break;
                    }
        default :                                               //http://ip/admin
                    {    
                        String message = WiFi.softAPmacAddress();
                        s += "<!DOCTYPE html><html><head><title>Digiti</title></head>";
                        s += "<body><form action='/admin' method='POST'><br /><H1>" + message + "</H1><H1> VERSION NOW : " + String(DIGITI_VERSION) + "</H1>";
                        s += "============================<br />";
                        s += "1 : <a href='/admin?han=1' text='Red'>CONTROL</a><br /><br />";     // bảng điều khiển
                        s += "2 : <a href='/admin?han=2' text='red'>WIFI STATUS</a><br /><br />";   // trạng thái liên quan tới mạng wifi
                        s += "3 : <a href='/admin?han=12' text='red'>CHECK UPDATE</a><br /><br />";  // nói chuyện với server
                        s += "4 : <a href='/admin?han=11' text='red'>SETUP</a><br /><br />";          // cài đặt liên quan tới cả blynk
                        s += "5 : <a href='/admin?han=13' text='red'>DATA</a><br /><br />";            // xuất data
                        s += "============================<br />";
                        s += "<br /><a href='/admin'>RELOAD</a><br />";
                        s += "</from></body></html>";
                        server.send(200,"text/html",s);
                        break;
                    }
    }

}

/*
API ESP8266
*/


void http_handle()
{
  #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] URI : %s", millis(), server.uri().c_str());
    Serial.printf("\n[%d] ARGUMENTS = %d ==> ", millis(), server.args());
    for (uint8_t i=0; i<server.args(); i++)
    {
        Serial.printf(" %s = %s ||",server.argName(i).c_str(),server.arg(i).c_str());
    }
    #endif

  int han = server.arg("han").toInt();

  switch (han)
  {
    case HAN_UPDATE:
          {                           // http://ip/handle?han=1&pin=2&value=1
            if (server.arg("value") != "")
            {
              if(server.arg("pin")=="0"||server.arg("pin")=="2"||server.arg("pin")=="4"||server.arg("pin")=="5"||server.arg("pin")=="12"||server.arg("pin")=="13"||server.arg("pin")=="14"||server.arg("pin")=="15"||server.arg("pin")=="16" ||server.arg("pin")=="00"||server.arg("pin")=="02"||server.arg("pin")=="04"||server.arg("pin")=="05")
              {
                digitalWrite(server.arg("pin").toInt(),server.arg("value").toInt());
                server.send(200,"text/plain","");
                break;
              } 
            }
            server.send(200,"text/plain","false");
            break;
          }
    case HAN_WIFI_STATUS:
          {                                                 // http://ip/handle?han=2
            String content = "";
            if ( WiFi.status() == WL_CONNECTED )
            {
              content += (WiFi.SSID());
            }
            server.send(200, "text/plain", content);
            break;
          }
    case HAN_PROJECT:                            // http://ip/handle?han=3
          {
            String content = "";
            for (int i = 0; i < 17; i++)
            {
              if(i==1||i==3||i == 6 || i == 7 || i==8 || i==9 || i==10 || i==11) continue;
              content += String(digitalRead(i));
            }
                server.send(200, "text/plain", content);
                break;
            }
    case HAN_DISCONNECT:                    // http://ip/handle?han=4
          {
            server.send(200, "text/plain", "true");
            WiFi.disconnect(true);  
            break;
          }
    case HAN_BLYNK_CONFIG:          // http://ip/handle?han=5
          {
            if(blynk_config())
            {
              server.send(200,"text/plain", "true");
              break;
            }
            server.send(200,"text/plain", "false");
            break;
          }
    case HAN_BLYNK_DISCONNECT:            // http://ip/handle?han=6
          {
            server.send(200, "text/plain", "true");
            Blynk.disconnect();
            break;
          }
    case HAN_CONNECT:                          // http://ip/handle?han=7&sta_ssid=DuongBui&sta_pass=19011980
          {
            if(server.arg("sta_ssid") != "")
            {
              server.send(200,"text/plain", "true"); delay(2);
              wifi_config_sta(server.arg("sta_ssid"),server.arg("sta_pass"));
              break;
            }
            server.send(200,"text/plain", "false");
            break;  
          }
    case HAN_DATA_WRITE:              //http://ip/handle?han=8&location=1&value=abcxyz
          { if(import_data(server.arg("location").toInt(),server.arg("value")))
            {
              server.send(200,"text/plain", "true");
              break;  
            }
            server.send(200,"text/plain", "false");
            break;
          }
    case HAN_DATA_READ:             //http://ip/handle?han=9&location=1
          { 
            server.send(200,"text/plain", export_data(server.arg("location").toInt()));
            break;
          }
    case HAN_UPDATE_SKETCH:
          {         //http://ip/handle?han=10
            server_handle();
            server.send(200,"text/plain","true");
            break;
          }
    case HAN_SCAN_NETWORKS:   //http://ip/handle?han=14   
          { 
            String s = "";
            int total_wifi = WiFi.scanNetworks(); delay(20);
            for ( int i = 0; i < total_wifi; ++i)
            {
              s += WiFi.SSID(i) + ",";
            }
            server.send(200,"text/plain",s);
          }
    default :
    {
          server.send(404, "text/plain", "error");  
    }
  }
}

void handleNotFound()
{
    String message = "File Not Found\n\n";
    message += "URI: ";
    message += server.uri();
    message += "\nMethod: ";
    message += (server.method() == HTTP_GET)?"GET":"POST";
    message += "\nArguments: ";
    message += server.args();
    message += "\n";
    for (uint8_t i=0; i<server.args(); i++)
    {
      message += " " + server.argName(i) + ": " + server.arg(i) + "\n";
    }
    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] %s", millis(), message.c_str());
    #endif
    server.send(404, "text/plain", message);
}



void check_server()
{
  static unsigned long time_cs = millis();
  if ( (unsigned long)millis() - time_cs > 86400000)
  {
    server_handle();
    time_cs = (unsigned long)millis();
  }
}

void server_handle()
{
  if(WiFi.status() == WL_CONNECTED)
  {
    String content = "/api/hardware.php?";
    content += "ap_mac=" + (WiFi.softAPmacAddress()) + "&";
    content += "version=" + String(DIGITI_VERSION);

    String result_access = api("http://hoangvanhieu182.000webhostapp.com" + content);

    #ifdef DEBUG_SERIAL
    Serial.printf("\n[%d] RETURN: %s", millis(), result_access.c_str());
    #endif
    
    if ( result_access != "false")
    {
      if (result_access.indexOf("http://hoangvanhieu182.000webhostapp.com") != -1)
      {
        t_httpUpdate_return ret = ESPhttpUpdate.update(result_access);
        #ifdef DEBUG_SERIAL
        switch(ret) {
                case HTTP_UPDATE_FAILED:
                    Serial.printf("HTTP_UPDATE_FAILD Error (%d): %s", ESPhttpUpdate.getLastError(), ESPhttpUpdate.getLastErrorString().c_str());
                    break;

                case HTTP_UPDATE_NO_UPDATES:
                    Serial.println("HTTP_UPDATE_NO_UPDATES");
                    break;

                case HTTP_UPDATE_OK:
                    Serial.println("HTTP_UPDATE_OK");
                    break;
            }
            #endif
      }
      if (result_access.length() == 32)
      {
        import_data(11,result_access);
      }
    }
  }
}


