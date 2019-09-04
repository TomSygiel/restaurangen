<?php

class Booking {
	private $connection;

	public function __construct($db) {
		$this->connection = $db;
	}

	public function deleteBooking($booking_ID) {
		$delete_booking = $this->connection->prepare(
			'DELETE
			FROM bookings 
			WHERE booking_ID = ?'
		);

		$booking_ID=htmlspecialchars(strip_tags($booking_ID));
		$delete_booking->bindParam(1, $booking_ID);
		
		if($delete_booking->execute()) {
			return true;
		}

		return false;
	}

	public function updateBooking($booking_row, $customer_row) {
		$statement = $this->connection->prepare(
			'UPDATE bookings
			SET guests = :guests, 
			sitting = :sitting,
			WHERE booking_ID = :booking_ID AND customer_ID = :customer_ID;

			UPDATE customers
			SET email = :email, 
			name = :name, 
			phone = :phone
			WHERE customer_ID = :customer_ID'
		);	
		
		$booking_ID = htmlspecialchars(
			strip_tags($booking_row->$booking_ID));
		$customer_ID = htmlspecialchars(
			strip_tags($booking_row->$customer_ID));
		$guests = htmlspecialchars(
			strip_tags($booking_row->$guests));
		$sitting = htmlspecialchars(
			strip_tags($booking_row->$sitting));
		$name = htmlspecialchars(
			strip_tags($customer_row->$name));
		$email = htmlspecialchars(
			strip_tags($customer_row->$email));
		$phone = htmlspecialchars(
			strip_tags($customer_row->$phone));

		$statement->bindParam(':booking_ID', $booking_ID);
		$statement->bindParam(':customer_ID', $customer_ID);
		$statement->bindParam(':guests', $guests);
		$statement->bindParam(':sitting', $sitting);
		$statement->bindParam(':name', $name);
		$statement->bindParam(':email', $email);
		$statement->bindParam(':phone', $phone);

		var_dump($statement);

		if($statement->execute()) {
			echo("true");
			return true;
		}

		echo("false");
		return false;
	}

	public function getBookedTables($date) {
		$statement = $this->connection->prepare(
			'SELECT * FROM bookings WHERE DATE(sitting) = :date'
		);
			
		$statement->execute([
			':date' => $date
		]);
		$all_bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $all_bookings;
	}

	public function getBookings() {
		// select all and prepare query statement
		$booking_results = $this->connection->prepare(
			'SELECT * FROM bookings
			LEFT JOIN customers
			ON bookings.customer_ID = customers.customer_ID'
		);

		// execute query
		$booking_results->execute();
		
		$all_bookings = $booking_results->fetchAll(PDO::FETCH_ASSOC);

		return $all_bookings;
	}

	public function createBooking($booking_row) {
		$statement = $this->connection->prepare(
			'INSERT INTO bookings (customer_ID, guests, sitting) 
			VALUES (:customer_ID, :guests, :sitting)'
		); 

		// sanitize
		$booking_row->$customer_ID = htmlspecialchars(
			strip_tags($booking_row->$customer_ID)
		);
		$booking_row->$guests = htmlspecialchars(
			strip_tags($booking_row->$guests)
		);
		$booking_row->$sitting = htmlspecialchars(
			strip_tags($booking_row->$sitting)
		);

		$statement->bindParam(':customer_ID', $booking_row->customer_ID);
		$statement->bindParam(':guests', $booking_row->guests);
		$statement->bindParam(':sitting', $booking_row->sitting);

		if ($statement->execute()) {
			return true;
		}

		return false;
	}
}

class BookingRow {
	public $booking_ID;
	public $customer_ID;
	public $guests;
	public $sitting;
}