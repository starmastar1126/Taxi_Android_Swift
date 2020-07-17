//
//  ExtArray.swift
//  PassengerApp
//
//  Created by NEW MAC on 07/11/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
extension Array {
    func chunked(by chunkSize:Int) -> [[Element]] {
        let groups = stride(from: 0, to: self.count, by: chunkSize).map {
            Array(self[$0..<[$0 + chunkSize, self.count].min()!])
        }
        return groups
    }
}
