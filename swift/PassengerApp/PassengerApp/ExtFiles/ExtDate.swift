//
//  ExtDate.swift
//  PassengerApp
//
//  Created by NEW MAC on 02/09/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
extension Date{
    func addedBy(minutes:Int) -> Date {
        return Calendar.current.date(byAdding: .minute, value: minutes, to: self)!
    }
    
    
    func addedBy(hours:Int) -> Date {
        return Calendar.current.date(byAdding: .hour, value: hours, to: self)!
    }
    
    
    func addedBy(months:Int) -> Date {
        return Calendar.current.date(byAdding: .month, value: months, to: self)!
    }
    
    func addedBy(days:Int) -> Date {
        return Calendar.current.date(byAdding: .day, value: days, to: self)!
    }
    func addedBy(seconds:Int) -> Date {
        var cal = Calendar.current
        cal.timeZone = TimeZone(identifier: DateFormatter().timeZone.identifier)!
        return cal.date(byAdding: .second, value: seconds, to: self)!
    }
}
