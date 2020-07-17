//
//  NSDictionaryExt.swift
//  PassengerApp
//
//  Created by NEW MAC on 26/12/16.
//  Copyright © 2016 BBCS. All rights reserved.
//

import Foundation
extension NSDictionary{
    
    func get(_ key:String) -> String{
        
        let value = self.value(forKey: key)
        
        if(value != nil){
            return String(describing: value!)
        }
        
        return ""
    }
    
    func getNsStr(_ key:String) -> NSString{
        
        let value = self.value(forKey: key)
        
        if(value != nil){
            return (value as! NSString)
        }
        
        return ""
    }
    
    func getObj(_ key:String) -> NSDictionary{
        
        let value = self.value(forKey: key)
        
        if(value != nil && (value! as? NSDictionary) != nil){
            return value! as! NSDictionary
        }
        
        return self
    }
    
    func getArrObj(_ key:String) -> NSArray{
        
        let value = self.value(forKey: key)
        
        if(value != nil && (value as? NSArray) != nil){
            return value! as! NSArray
        }
        
        return [String]() as NSArray
    }
    
    func isKeyExist(_ key:String) -> Bool{
        let value = self.value(forKey: key)
        
        if(value != nil){
            return true
        }
        return false
    }
    
    func convertToJson() -> String{
        
        if let theJSONData = try? JSONSerialization.data(
            withJSONObject: self,
            options: []) {
            let theJSONText = String(data: theJSONData, encoding: .utf8)
            
            return theJSONText!
        }
        
        return ""
    }
}
